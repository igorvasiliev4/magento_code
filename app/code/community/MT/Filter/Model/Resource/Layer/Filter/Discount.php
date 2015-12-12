<?php
/**
 * @category    MT
 * @package     MT_Filter
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Filter_Model_Resource_Layer_Filter_Discount extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct(){
        $this->_init('catalog/product_index_price', 'entity_id');
    }

    /**
     * @param $filter Mage_Catalog_Block_Layer_View
     * @param $value mixed
     * @return $this
     */
    public function applyFilterToCollection($filter, $value){
        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $select->where('price_index.price > price_index.final_price');

        return $this;
    }

    /**
     * @param $filter Mage_Catalog_Model_Layer_Filter_Abstract
     * @return array
     */
    public function getCount($filter){
        $connection = $this->_getReadAdapter();

        /* @var $select Varien_Db_Select */
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $fromPart = $select->getPart('from');
        foreach ($fromPart as $alias => $from){
            if ($from['tableName'] == 'catalog_product_index_price'){
                $fromPart[$alias]['joinCondition'] = join(' AND ', array($from['joinCondition'], "{$alias}.price > {$alias}.final_price"));
            }
        }
        $select->setPart('from', $fromPart);
        $select->columns(array('count' => new Zend_Db_Expr("COUNT(price_index.entity_id)")));

        return $connection->fetchRow($select);
    }
}
