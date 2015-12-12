<?php

class Magehit_Faq_Model_Mysql4_Faqitem extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the faqitem_id refers to the key field in your database table.
        $this->_init('faq/faqitem', 'faqitem_id');
    }
    
	protected function _afterSave(Mage_Core_Model_Abstract $object){
		$condition = $this->_getWriteAdapter()->quoteInto('faqitem_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('faqitem_store'), $condition);

		if (!$object->getData('stores'))
		{
			$storeArray = array();
            $storeArray['faqitem_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('faqitem_store'), $storeArray);
		}
		else
		{
			foreach ((array)$object->getData('stores') as $store) {
				$storeArray = array();
				$storeArray['faqitem_id'] = $object->getId();
				$storeArray['store_id'] = $store;
				$this->_getWriteAdapter()->insert($this->getTable('faqitem_store'), $storeArray);
			}
		}

        return parent::_afterSave($object);
    }
    protected function _beforeDelete(Mage_Core_Model_Abstract $object){
		
		// Cleanup stats on blog delete
		$adapter = $this->_getReadAdapter();
		// 1. Delete testimonial/store
		$adapter->delete($this->getTable('faq/faqitem_store'), 'faqitem_id='.$object->getId());

	}        
	
}