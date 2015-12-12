<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Filter_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price{
    public function getItemsCount(){
        return true;
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Price
     */
    protected function _getResource(){
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('mtfilter/layer_filter_price');
        }
        return $this->_resource;
    }

    /**
     * Apply price range filter to collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param $filterBlock
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock){
        if (Mage::helper('mtfilter')->isPriceEnable() && version_compare(Mage::getVersion(), '1.7.0.0') < 0){
            /**
             * Filter must be string: $index,$range
             */
            $filter = $request->getParam($this->getRequestVar());
            if (!$filter) {
                return $this;
            }

            $filter = explode('-', $filter);
            if (count($filter) != 2) {
                return $this;
            }

            list($index, $range) = $filter;

            if (is_numeric($index) && is_numeric($range)) {
                $this->setPriceRange((int)$range);
                $this->setPriceRangeCustom($filter);

                $this->_applyToCollection($range, $index);
                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_renderItemLabelCustom($index, $range), $filter)
                );

                $this->_items = array();
            }
            return $this;
        }else{
            parent::apply($request, $filterBlock);
        }
    }

    /**
     * Prepare text of range label
     *
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return string
     */
    protected function _renderItemLabelCustom($fromPrice, $toPrice){
        $store = Mage::app()->getStore();
        $formattedFromPrice  = $store->formatPrice($fromPrice);
        if ($toPrice === '') {
            return Mage::helper('catalog')->__('%s and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice) {
            return $formattedFromPrice;
        } else {
            return Mage::helper('catalog')->__('%s - %s', $formattedFromPrice, $store->formatPrice($toPrice));
        }
    }
}
