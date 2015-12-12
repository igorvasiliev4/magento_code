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

class Soczed_Less_Model_System_Config_Backend_Variables
    extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    protected function _beforeSave()
    {
        // Remove empty value
        if (is_array($value = $this->getValue())) {
            unset($value['__empty']);
        } else {
            $value = array();
        }
        
        // Prepare resulting value
        $result = array();
        
        foreach ($value as $key => $variable) {
            // Only save if all values are set
            if (isset($variable['code'])
                && (trim($variable['code']) !== '')
                && !isset($result[$variable['code']])
                && isset($variable['value'])
                && (trim($variable['value']) !== '')) {
                $result[$variable['code']] = $variable;
            }
        }
        
        $this->setValue($result);
        return parent::_beforeSave();
    }
}