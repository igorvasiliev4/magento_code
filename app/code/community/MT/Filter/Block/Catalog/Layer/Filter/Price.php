<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Filter_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price{
    protected $_hash;
    protected $_maxPrice;
    protected $_currencySymbol;
    protected $_maxPriceCategory;

    public function __construct(){
        parent::__construct();
        if (Mage::helper('mtfilter')->isPriceEnable()){
            $this->setTemplate('mt/filter/price.phtml');
            $this->_hash = Mage::helper('core')->uniqHash('slider-');
            $this->_filterModelName = 'mtfilter/layer_filter_price';
        }
    }

    public function getHash($suffix=null){
        return $this->_hash . $suffix;
    }

    public function getValues(){
        $priceParams = explode('-', $this->getRequest()->getQuery($this->_filter->getRequestVar()));
        if (count($priceParams) == 2){
            return $priceParams;
        }
        return null;
    }

    public function getRange(){
        return array(0, $this->getMaxPriceCategory());
    }

    public function getMaxPriceCategory(){
        if (!$this->_maxPriceCategory){
            $this->_maxPriceCategory = $this->getLayer()->getProductCollection()->getMaxPriceCategory();
        }
        return $this->_maxPriceCategory;
    }

    public function getMaxPrice(){
        if (!$this->_maxPrice){
            $this->_maxPrice = $this->getLayer()->getProductCollection()->getMaxPrice();
        }
        return $this->_maxPrice;
    }

    public function getCurrencySymbol(){
        if (!$this->_currencySymbol){
            $this->_currencySymbol = Mage::app()->getLocale()->currency(
                Mage::app()->getStore()->getCurrentCurrencyCode()
            )->getSymbol();
        }
        return $this->_currencySymbol;
    }

    public function getConfig(){
        $obj = new stdClass();
        $obj->id = $this->getHash();
        $range = $this->getRange();
        $obj->min = floor($range[0]);
        $obj->max = ceil($range[1]);
        $obj->values = $this->getValues() ? $this->getValues() : array($obj->min, $obj->max);
        $obj->symbol = $this->getCurrencySymbol();
        $obj->url = $this->getCurrentUrl();
        return json_encode($obj);
    }

    public function getCurrentUrl(){
        return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }
}
