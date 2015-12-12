<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Layer Decimal attribute Filter Resource Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Ajaxlist_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal {

    /**
     * Retrieve array with products counts per range
     *
     * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
     * @param int $range
     * @return array
     */
    public function getCount($filter, $range) {


        $collection = $filter->getLayer()->getProductCollection();

        // clone select from collection with filters
        $select = clone $collection->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $attributeId = $filter->getAttributeModel()->getId();
        $storeId = $collection->getStoreId();

        $select->join(
                array('decimal_index' => $this->getMainTable()), 'e.entity_id = decimal_index.entity_id' .
                ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.attribute_id = ?', $attributeId) .
                ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.store_id = ?', $storeId), array()
        );

        //return $select;
//$select     = $this->_getSelect($filter);
        // clone select from collection with filters
        //$select = clone $filter->getLayer()->getProductCollection()->getSelect();
        // reset columns, order and limitation conditions
        /* $select->reset(Zend_Db_Select::COLUMNS);
          $select->reset(Zend_Db_Select::ORDER);
          $select->reset(Zend_Db_Select::LIMIT_COUNT);
          $select->reset(Zend_Db_Select::LIMIT_OFFSET); */


        $adapter = $this->_getReadAdapter();

        //$connection = $this->_getReadAdapter();
        $attribute = $filter->getAttributeModel();
        //$tableAlias = $attribute->getAttributeCode() . '_idy';


        $_where = $select->getPart(Zend_Db_Select::WHERE);
        //$_all_conditions = $this->getConditions();
        //print_r($_where);
        foreach ($_where as $index => $condition) {
            if (strpos($condition, $attribute->getAttributeCode() . "_idx") >= 0)
                unset($_where[$index]);
        }
        $select->setPart(Zend_Db_Select::WHERE, $_where);



        $countExpr = new Zend_Db_Expr("COUNT(*)");
        $rangeExpr = new Zend_Db_Expr("FLOOR(decimal_index.value / {$range}) + 1");

        $select->columns(array(
            'decimal_range' => $rangeExpr,
            'count' => $countExpr
        ));
        $select->group($rangeExpr);

        return $adapter->fetchPairs($select);
    }

    /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
     * @param float $range
     * @param int $index
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Decimal
     */
    public function applyFilterToCollection($filter, $ranges, $index) {
        $collection = $filter->getLayer()->getProductCollection();
        $attribute = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId())
        );

        $collection->getSelect()->join(
                array($tableAlias => $this->getMainTable()), implode(' AND ', $conditions), array()
        );

        $_conditions = array();

        foreach ($ranges as $_interval) {

            $index = (int) $_interval[0];
            $range = (int) $_interval[1];

            $_condition = " ( {$tableAlias}.value >= " . ($range * ($index - 1)) . " AND  {$tableAlias}.value < " . ($range * $index) . " ) ";

            /* $collection->getSelect()
              ->where("{$tableAlias}.value >= ?", ($range * ($index - 1)))
              ->where("{$tableAlias}.value < ?", ($range * $index));
             */
            $_conditions[] = $_condition;
        }
        if (count($_conditions)) {
            $_att_condition = join(" OR ", $_conditions);
            $collection->getSelect()->where(" ( " . $_att_condition . " ) ");
        }
        return $this;
    }

}
