<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Filter_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection{
    const INDEX_TABLE_ALIAS = 'price_index';
    protected $_priceExpression;
    protected $_additionalPriceExpression;
    protected $_catalogPreparePriceSelect;

    /**
     * Get max price for current category
     *
     * @return float
     */
    public function getMaxPriceCategory(){
        $select = clone $this->getSelect();
        $priceExpression = $this->getPriceExpression($select) . ' ' . $this->getAdditionalPriceExpression($select);
        $sqlEndPart = ') * ' . $this->getCurrencyRate() . ', 2)';
        $select = $this->_getSelectCountSql($select, false);
        $select->columns('ROUND(MAX(' . $priceExpression . $sqlEndPart);
        $select->columns('ROUND(MIN(' . $priceExpression . $sqlEndPart);
        $select->reset(Zend_Db_Select::WHERE);
        $fromPart = $select->getPart('from');
        foreach ($fromPart as $from => $data){
            if (strpos($from, '_idx') > 0){
                unset($fromPart[$from]);
            }
        }
        $select->setPart('from', $fromPart);
        $row = $this->getConnection()->fetchRow($select, $this->_bindParams, Zend_Db::FETCH_NUM);
        return (float)$row[1];
    }

    /**
     * Get SQL for get record count
     *
     * @param $select
     * @param bool $resetLeftJoins
     * @return Varien_Db_Select
     * @version 1.6.2.0, 1.7.0.2
     */
    protected function _getSelectCountSql($select = null, $resetLeftJoins = true){
        $this->_renderFilters();
        $countSelect = (is_null($select)) ?
            $this->_getClearSelect() :
            $this->_buildClearSelect($select);
        $countSelect->columns('COUNT(DISTINCT e.entity_id)');
        if ($resetLeftJoins) {
            $countSelect->resetJoinLeft();
        }
        return $countSelect;
    }

    /**
     * Build clear select
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     * @version 1.6.2.0, 1.7.0.2
     */
    protected function _buildClearSelect($select = null){
        if (is_null($select)) {
            $select = clone $this->getSelect();
        }
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);

        return $select;
    }

    /**
     * Get currency rate
     *
     * @return float
     * @version 1.6.2.0, 1.7.0.2
     */
    public function getCurrencyRate(){
        return Mage::app()->getStore($this->getStoreId())->getCurrentCurrencyRate();
    }

    /**
     * Get price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return string
     * @version 1.6.2.0, 1.7.0.2
     */
    public function getPriceExpression($select){
        if (is_null($this->_priceExpression)) {
            $this->_preparePriceExpressionParameters($select);
        }
        return $this->_priceExpression;
    }

    /**
     * Get additional price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return string
     * @version 1.6.2.0, 1.7.0.2
     */
    public function getAdditionalPriceExpression($select){
        if (is_null($this->_additionalPriceExpression)) {
            $this->_preparePriceExpressionParameters($select);
        }
        return $this->_additionalPriceExpression;
    }

    /**
     * Prepare additional price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return Mage_Catalog_Model_Resource_Product_Collection
     * @version 1.6.2.0, 1.7.0.2
     */
    protected function _preparePriceExpressionParameters($select){
        // prepare response object for event
        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());
        $tableAliases = array_keys($select->getPart(Zend_Db_Select::FROM));
        if (in_array(self::INDEX_TABLE_ALIAS, $tableAliases)) {
            $table = self::INDEX_TABLE_ALIAS;
        } else {
            $table = reset($tableAliases);
        }

        // prepare event arguments
        $eventArgs = array(
            'select'          => $select,
            'table'           => $table,
            'store_id'        => $this->getStoreId(),
            'response_object' => $response
        );

        Mage::dispatchEvent('catalog_prepare_price_select', $eventArgs);

        $additional   = join('', $response->getAdditionalCalculations());
        $this->_priceExpression = $table . '.min_price';
        $this->_additionalPriceExpression = $additional;
        $this->_catalogPreparePriceSelect = clone $select;

        return $this;
    }
}
