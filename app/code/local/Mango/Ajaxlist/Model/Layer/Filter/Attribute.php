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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layer attribute filter
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mango_Ajaxlist_Model_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute {

    /**
     * Construct attribute filter
     *
     */
    public function __construct() {
        parent::__construct();
        //   $this->_requestVar = 'attribute';
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        $filter = $request->getParam($this->_requestVar);
        if (preg_match('/^[0-9,]+$/', $filter)) {
            $filter = array_unique(explode(',', $filter));
        }
        if (!is_array($filter) || !count($filter)) {
            return $this;
        }
        
        $text = $this->_getOptionText($filter);
        
        if ($filter && $text) {
            $this->_getResource()->applyFilterToCollection($this, $filter);
            foreach ($filter as $option)
                $this->getLayer()->getState()->addFilter($this->_createItem($text[$option], $option));
            //$this->_items = array();
        }
        return $this;
    }

    protected function _getOptionText($filter) {
        $_optionText = array();
        foreach ($filter as $optionId)
            $_optionText[$optionId] = $this->getAttributeModel()->getFrontend()->getOption($optionId);
        return $_optionText;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData() {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();
        $key = $this->getLayer()->getStateKey() . '_' . $this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        /* fix to show also selected items with count = 0  */
        $_url_param = Mage::app()->getRequest()->getParam($attribute->getAttributeCode());
        $_filter_values = array();
        if (preg_match('/^[0-9,]+$/', $_url_param)) {
            $_filter_values = explode(',', $_url_param);
        } elseif ((int) $_url_param > 0) {
            $_filter_values[] = $_url_param;
        }


        $options = $attribute->getFrontend()->getSelectOptions();
        $optionsCount = $this->_getResource()->getCount($this);
        //$optionsCount = $this->_getCount($attribute);
        $data = array();
//print_r($options);
        foreach ($options as $option) {

            $_option_used = in_array( $option['value'] ,  $_filter_values );

            if (is_array($option['value'])) {
                continue;
            }
            if (!Mage::getStoreConfig("ajaxlist/ajaxlist/show_no_count") && (!isset($optionsCount[$option['value']]) || !$optionsCount[$option['value']]) && !$_option_used ) {
                continue;
            }
            
            if (Mage::helper('core/string')->strlen($option['value'])) {
                // Check filter type
               // if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS || $_option_used ) {
                    // if (!empty($optionsCount[$option['value']])) {
                 /*   $data[] = array(
                        'label' => $option['label'],
                        'value' => $option['value'],
                        'count' => $optionsCount[$option['value']],
                    );*/
                    // }
               // } else {
                    $data[] = array(
                        'label' => $option['label'],
                        'value' => $option['value'],
                        'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                    );
               // }
            }
        }
        $tags = array(
            Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $attribute->getId()
        );
        $tags = $this->getLayer()->getStateTags($tags);
        $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        // }
        return $data;
    }

    /*    public function getItemsCount(){
      return 1;
      } */
    /* public function getResetValue()
      {
      return "----";
      } */

    protected function _getCount($attribute) {
        $optionsCount = array();
        // clone select from collection with filters
        $select = clone $this->_getBaseCollectionSql();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $connection = $this->_getResource()->getReadConnection();
        $tableAlias = $attribute->getAttributeCode() . '_idy';
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $this->getStoreId()),
        );
        $select
                ->join(
                        array($tableAlias => $this->_getResource()->getMainTable()),
                        join(' AND ', $conditions),
                        array('value', 'count' => "COUNT({$tableAlias}.entity_id)"))
                ->group("{$tableAlias}.value");
        $optionsCount = $connection->fetchPairs($select);
        return $optionsCount;
    }

}
