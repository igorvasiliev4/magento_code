<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Attribute_Model_System_Config_Source_Position{
    public function toOptionArray(){
        return array(
            array('value' => 'right', 'label' => Mage::helper('mtattribute')->__('Right')),
            array('value' => 'left', 'label' => Mage::helper('mtattribute')->__('Left')),
            array('value' => 'top', 'label' => Mage::helper('mtattribute')->__('Top')),
            array('value' => 'bottom', 'label' => Mage::helper('mtattribute')->__('Bottom')),
            array('value' => 'inside', 'label' => Mage::helper('mtattribute')->__('Inside'))
        );
    }
}