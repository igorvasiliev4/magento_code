<?php

class Pcarousel_Pcarousel_Model_Pcarousel extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pcarousel/pcarousel');
    }
}