<?php
class Technical_Pacsoft_Model_Options_Enable
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('pdpacsoft')->__('Disable')),
            array('value' => 1, 'label' => Mage::helper('pdpacsoft')->__('Enable'))
        );
    }
}