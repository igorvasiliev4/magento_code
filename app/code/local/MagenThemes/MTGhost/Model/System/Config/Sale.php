<?php
class MagenThemes_MTGhost_Model_System_Config_Sale
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'sale', 'label'=>Mage::helper('adminhtml')->__('Sale')),
            array('value' => 'imagesale', 'label'=>Mage::helper('adminhtml')->__('Image')),
            array('value' => '', 'label'=>Mage::helper('adminhtml')->__('No')),
        );
    }
}