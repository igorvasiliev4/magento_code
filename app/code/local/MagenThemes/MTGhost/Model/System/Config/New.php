<?php
class MagenThemes_MTGhost_Model_System_Config_New
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'new', 'label'=>Mage::helper('adminhtml')->__('New')),
            array('value' => 'imagenew', 'label'=>Mage::helper('adminhtml')->__('Image')),
            array('value' => '', 'label'=>Mage::helper('adminhtml')->__('No')),
        );
    }
}