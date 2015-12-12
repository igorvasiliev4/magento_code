<?php

class Pcarousel_Pcarousel_Model_Mysql4_Pcarousel_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pcarousel/pcarousel');
    }
}