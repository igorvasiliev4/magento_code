<?php

class AW_Dev_Model_Observers extends Mage_Core_Model_Abstract
{
    public function _helper($name = null)
    {
        return Mage::helper('awdev' . ($name ? '/' . $name : ''));
    }

    public function observePredispatch($observer)
    {
        $this->_helper()->disableNotifications();
    }
}
