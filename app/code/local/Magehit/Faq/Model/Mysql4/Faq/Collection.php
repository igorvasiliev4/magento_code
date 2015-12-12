<?php

class Magehit_Faq_Model_Mysql4_Faq_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/faq');
    }
    public function addEnableFilter($status)
    {
        $this->getSelect()
            ->where('status = ?', $status);
        return $this;
    }

    public function addStoreFilter($store)
    {
		if (!Mage::app()->isSingleStoreMode()) {
			if ($store instanceof Mage_Core_Model_Store) {
				$store = array($store->getId());
			}
	
			$this->getSelect()->join(
				array('store_table' => $this->getTable('faq_store')),
				'main_table.faq_id = store_table.faq_id',
				array()
			)
			->where('store_table.store_id in (?)', array(0, $store));
	
			return $this;
		}
		return $this;
	}    
}