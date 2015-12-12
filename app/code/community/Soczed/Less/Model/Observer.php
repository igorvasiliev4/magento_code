<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Soczed
 * @package    Soczed_Less
 * @copyright  Copyright (c) 2012 Soczed <magento@soczed.com> (Benoît Leulliette <benoit@soczed.com>)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

set_include_path(get_include_path().PS.Mage::getBaseDir('lib').DS.'Soczed'.DS.'less');
require_once('lessc.inc.php');

class Soczed_Less_Model_Observer
{
    protected function _getHelper()
    {
        return Mage::helper('less');
    }
    
    protected function _getConfigHelper()
    {
        return Mage::helper('less/config');
    }
    
    protected function _checkWritableFile($file)
    {
        $pathinfo = pathinfo($file);
        
        if (empty($pathinfo['dirname']) || !is_writable($pathinfo['dirname'])) {
            return $this->_getHelper()->__('Directory is not writable');
        }
        if (is_file($file) && !is_writable($file)) {
            return $this->_getHelper()->__('File is not writable');
        }
        
        return true;
    }
    
    protected function _getLessVariables($file)
    {
        // Base variables
        // @todo complete this array with any variable that could be useful
        $variables = array();
        
        // Get additional variables
        $response = new Varien_Object(array('less_variables' => array()));
        
        Mage::dispatchEvent(
            'soczed_less_additional_variables',
            array(
                'response'  => $response,
                'file_name' => $file,
            )
        );
        
        if (is_array($additional = $response->getLessVariables())) {
            $variables = array_merge($variables, $additional);
        }
        
        return $variables;
    }
    
    protected function _getLessFunctions($file)
    {
        // Base functions
        // @todo complete this array with any function that could be useful
        $functions = array();
        
        // Get additional functions
        $response = new Varien_Object(array('less_functions' => array()));
        
        Mage::dispatchEvent(
            'soczed_less_additional_functions',
            array(
                'response'  => $response,
                'file_name' => $file,
            )
        );
        
        if (is_array($additional = $response->getLessFunctions())) {
            $functions = array_merge($functions, $additional);
        }
        
        return $functions;
    }
    
    
    public function beforeLayoutRender($observer)
    {
        if (!$this->_getConfigHelper()->isEnabled()) {
            return;
        }
        
        $layout = Mage::getSingleton('core/layout');
        
        if (($head = $layout->getBlock('head'))
            && ($head instanceof Mage_Page_Block_Html_Head)) {
            $baseJsDir     = Mage::getBaseDir() . DS . 'js' . DS;
            $designPackage = Mage::getDesign();
            $newItems      = $head->getData('items');
            $globalVars    = $this->_getConfigHelper()->getGlobalVariables();
            
            // Cache by file path
            $filesCollection = Mage::getModel('less/file')
                ->getCollection()
                ->load();
            
            $filesIds = array_flip($filesCollection->toOptionHash());
            
            foreach ($newItems as $key => $item) {
                if (in_array($item['type'], array('js_css', 'skin_css'))) {
                    // CSS file
                    if (substr($item['name'], -5) == '.less') {
                        // LESS file
                        if ($item['type'] == 'js_css') {
                            $lessFile = $baseJsDir . $item['name'];
                        } else {
                            $lessFile = $designPackage->getFilename($item['name'], array('_type' => 'skin'));
                        }
                        $baseFile = ltrim(str_replace(Mage::getBaseDir(), '', $lessFile), DS);
                        $cssFile  = substr($lessFile, 0, -5) . '.css';
                        
                        try {
                            // Init file config
                            if (isset($filesIds[$baseFile])) {
                                $isNewModel      = false;
                                $model           = $filesCollection->getItemById($filesIds[$baseFile]);
                                $oldCache        = $model->getCache();
                                $forceRebuild    = (bool)$model->getForceRebuild();
                                $customVars      = $model->getCustomVariables();
                                $useGlobalVars   = (bool)$model->getUseGlobalVariables();
                                $forceGlobalVars = (bool)$model->getForceGlobalVariables();
                            } else {
                                $isNewModel      = true;
                                $model           = null;
                                $oldCache        = null;
                                $forceRebuild    = false;
                                $customVars      = array();
                                $useGlobalVars   = true;
                                $forceGlobalVars = false;
                            }
                            
                            // Get all needed variables for current file
                            if (is_array($customVars)) {
                                $oldVars    = $customVars;
                                $customVars = array();
                                
                                foreach ($oldVars as $oldVar) {
                                    $customVars[$oldVar['code']] = $oldVar['value'];
                                }
                            } else {
                                $customVars = array();
                            }
                            if ($useGlobalVars) {
                                $variables  = array_merge(
                                    ($forceGlobalVars  ? $customVars : $globalVars),
                                    ($forceGlobalVars ? $globalVars : $customVars)
                                );
                            } else {
                                $variables = $customVars;
                            }
                            $variables = array_merge($variables, $this->_getLessVariables($item['name']));
                            
                            // Compile if needed (depends on cache and rebuild flag)
                            $oldCache = (is_array($oldCache) ? $oldCache : $lessFile);
                            
                            try {
                                $newCache = lessc::cexecute(
                                    $oldCache,
                                    $forceRebuild,
                                    $variables,
                                    $this->_getLessFunctions($item['name'])
                                );
                            } catch (Exception $e) {
                                if ($this->_getConfigHelper()->getShowErrors()) {
                                    if (!is_string($result = $this->_checkWritableFile($cssFile))) {
                                        file_put_contents($cssFile, "\n/* ".$e->getMessage()." */\n", FILE_APPEND);
                                    }
                                }
                                throw $e;
                            }
                            
                            if (!is_array($oldCache) || ($newCache['updated'] > $oldCache['updated'])) {
                                if (!is_string($result = $this->_checkWritableFile($cssFile))) {
                                    file_put_contents($cssFile, $newCache['compiled']);
                                } else {
                                    Mage::throwException($result);
                                }
                                if ($isNewModel) {
                                    $model = Mage::getModel('less/file')->setPath($baseFile);
                                }
                                // Won't be further needed and takes most of the place
                                unset($newCache['compiled']); 
                                $model->setCache($newCache)->save();
                            }
                        } catch (Exception $e) {
                            Mage::logException($e);
                        }
                        
                        // Force adding the CSS file instead of Less one
                        $newItems[$key]['name'] = substr($item['name'], 0, -5) . '.css';
                    }
                }
            }
            
            // Replace old items with parsed ones
            $head->setData('items', $newItems);
        }
    }
}