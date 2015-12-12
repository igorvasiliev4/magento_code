<?php
/**
 * @category    MT
 * @package     MT_Extensions
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Extensions_Block_Adminhtml_Widget_Form_Element_Dependence
    extends Mage_Adminhtml_Block_Widget_Form_Element_Dependence{

    /**
     * Register field name dependence one from each other by specified values
     *
     * @param string $fieldName
     * @param string $fieldNameFrom
     * @param string|array $refValues
     * @return MT_Extensions_Block_Adminhtml_Widget_Form_Element_Dependence
     */
    public function addFieldDependence($fieldName, $fieldNameFrom, $refValues)
    {
        $this->_depends[$fieldName][$fieldNameFrom] = $refValues;
        return $this;
    }

    /**
     * HTML output getter
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_depends) {
            return '';
        }
        return '<script type="text/javascript"> new MT.FormElementDependenceController('
        . $this->_getDependsJson()
        . ($this->_configOptions ? ', ' . Mage::helper('core')->jsonEncode($this->_configOptions) : '')
        . '); </script>';
    }
}
