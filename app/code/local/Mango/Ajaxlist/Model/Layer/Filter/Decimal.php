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
 * Catalog Layer Decimal Attribute Filter Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Ajaxlist_Model_Layer_Filter_Decimal extends Mage_Catalog_Model_Layer_Filter_Decimal {
    /**
     * Apply decimal range filter to product collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Decimal
     */
    /* public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
      {
      parent::apply($request, $filterBlock);
      $this->_items = null;
      return $this;
      } */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        //parent::apply($request, $filterBlock);
        /**
         * Filter must be string: $index, $range
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }
        $filters = explode(',', $filter);
        /* if (count($filter) != 2) {
          return $this;
          } */
        $_intervals = array();
        foreach ($filters as $param) {
            $_interval = explode("-", $param);
            list($index, $range) = $_interval;
            if ((int) $index && (int) $range) {
                $_intervals[] = $_interval;
                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_renderItemLabel($range, $index), $filter)
            );
            }
        }
        //foreach ($_intervals as $_interval) {
          //  list($index, $range) = $_interval;
            //if ((int)$index && (int)$range) {
            //$this->setRange((int)$range);
            
        //}
        //}
        $this->_getResource()->applyFilterToCollection($this, $_intervals, $index);
        $this->_items = null; /* for filters to appear on layered navigation */
        return $this;
    }
    /**
     * Retrieve data for build decimal filter items
     *
     * @return array
     */
    protected function _getItemsData() {
        $key = $this->_getCacheKey();
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $data = array();
            $range = $this->getRange();
            $dbRanges = $this->getRangeItemCounts($range);
            foreach ($dbRanges as $index => $count) {
                $data[] = array(
                    'label' => $this->_renderItemLabel($range, $index),
                    'value' => $index . '-' . $range,
                    'count' => $count,
                );
            }
        }
        return $data;
    }
}
