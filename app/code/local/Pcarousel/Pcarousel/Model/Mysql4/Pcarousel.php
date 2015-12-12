<?php

class Pcarousel_Pcarousel_Model_Mysql4_Pcarousel extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the pcarousel_id refers to the key field in your database table.
        $this->_init('pcarousel/pcarousel', 'pcarousel_id');
    }
}