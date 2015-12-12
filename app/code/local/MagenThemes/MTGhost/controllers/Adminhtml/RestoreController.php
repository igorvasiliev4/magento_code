<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package Mato Framework
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       ZooExtension.com
 * @email        magento@cleversoft.co
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class MagenThemes_MTGhost_Adminhtml_RestoreController extends Mage_Adminhtml_Controller_Action
{

    protected $_stores;
    protected $_clear;

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('magenthemes/mtghost/restore');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('magenthemes/mtghost/restore')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Restore Defaults'), Mage::helper('adminhtml')->__('Restore Defaults'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_title($this->__('MagenThemes'))
            ->_title($this->__('MTGhost'))
            ->_title($this->__('Restore Defaults'));
        $this->_addContent($this->getLayout()->createBlock('mtghost/adminhtml_restore_edit'));
        $this->renderLayout();
    }

    public function restoreAction()
    {
        $this->_stores = $this->getRequest()->getParam('stores', array(0));
        $this->_clear = $this->getRequest()->getParam('clear_scope', true);
        if ($this->_clear) {
            if ( !in_array(0, $this->_stores) )
                $stores[] = 0;
        }
	    try {
		    $defaults = new Varien_Simplexml_Config();
            $defaults->loadFile(Mage::getBaseDir().'/app/code/local/MagenThemes/MTGhost/etc/config.xml');
            $this->_restoreSettings($defaults->getNode('default/mtghost_design')->children(), 'mtghost_design');
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('Default Settings for MTGhost Design Theme has been restored.'));
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occurred while restoring theme settings.'));
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    private function _restoreSettings($items, $path)
    {
        $websites = Mage::app()->getWebsites();
        $stores = Mage::app()->getStores();
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->_restoreSettings($item->children(), $path.'/'.$item->getName());
            } else {
                if ($this->_clear) {
                    Mage::getConfig()->deleteConfig($path.'/'.$item->getName());
                    foreach ($websites as $website) {
                        Mage::getConfig()->deleteConfig($path.'/'.$item->getName(), 'websites', $website->getId());
                    }
                    foreach ($stores as $store) {
                        Mage::getConfig()->deleteConfig($path.'/'.$item->getName(), 'stores', $store->getId());
                    }
                }
                foreach ($this->_stores as $store) {
                    $scope = ($store ? 'stores' : 'default');
                    Mage::getConfig()->saveConfig($path.'/'.$item->getName(), (string)$item, $scope, $store);
                }
            }
        }
    }

}