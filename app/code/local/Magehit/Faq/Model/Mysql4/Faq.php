<?php

class Magehit_Faq_Model_Mysql4_Faq extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the faq_id refers to the key field in your database table.
        $this->_init('faq/faq', 'faq_id');
    }
    
	protected function _afterSave(Mage_Core_Model_Abstract $object){
		$condition = $this->_getWriteAdapter()->quoteInto('faq_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('faq_store'), $condition);

		if (!$object->getData('stores'))
		{
			$storeArray = array();
            $storeArray['faq_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('faq_store'), $storeArray);
		}
		else
		{
			foreach ((array)$object->getData('stores') as $store) {
				$storeArray = array();
				$storeArray['faq_id'] = $object->getId();
				$storeArray['store_id'] = $store;
				$this->_getWriteAdapter()->insert($this->getTable('faq_store'), $storeArray);
			}
		}

        return parent::_afterSave($object);
    }
    protected function _beforeDelete(Mage_Core_Model_Abstract $object){
		
		// Cleanup stats on blog delete
		$adapter = $this->_getReadAdapter();
		// 1. Delete testimonial/store
		$adapter->delete($this->getTable('faq/faq_store'), 'faq_id='.$object->getId());

	}        
	
}