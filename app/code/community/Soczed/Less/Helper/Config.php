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

class Soczed_Less_Helper_Config
    extends Mage_Core_Helper_Abstract
{
    const XML_GENERAL_ENABLED     = 'less/general/enabled';
    const XML_GENERAL_VARIABLES   = 'less/general/variables';
    const XML_GENERAL_SHOW_ERRORS = 'less/general/show_errors';
    
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_GENERAL_ENABLED);
    }
    
    public function getGlobalVariables()
    {
        $variables = Mage::getStoreConfig(self::XML_GENERAL_VARIABLES);
        $variables = (!is_array($variables) ? @unserialize($variables) : $variables);
        
        if (is_array($variables)) {
            $result = array();
            
            foreach ($variables as $variable) {
                $result[$variable['code']] = $variable['value'];
            }
            
            return $result;
        } else {
            return array();
        }
    }
    
    public function getShowErrors()
    {
        return Mage::getStoreConfigFlag(self::XML_GENERAL_SHOW_ERRORS);
    }
}