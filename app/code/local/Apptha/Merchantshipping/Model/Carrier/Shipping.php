<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.6
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */

/**
 * Seller shipping calculation for per item basis in cart 
 */
class Apptha_Merchantshipping_Model_Carrier_Shipping extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
    /**
     * Assigned the $_code as protected with value as 'apptha'
     */
    protected $_code = 'apptha';
    /**
     * Function to calculate the shipping per item in cart
     *
     * Core function shipping request data
     *
     * @param
     *            int Mage_Shipping_Model_Rate_Request $request
     *            
     *            This function will return the calculated shipping rate
     * @return int
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        $this->getCarrierStatus();
        $quote = Mage::getSingleton ( 'checkout/session' )->getQuote ();
        $total = $totalshipamount = '';
        foreach ( $quote->getAllItems () as $quote_item ) {
            $checkingForVirtual = $this->checkingForVirtual ( $quote_item );           
            if ($checkingForVirtual == 1) {
            	continue;
            }            
            $checkingForHasChildren = $this->checkingForHasChildren ( $quote_item );
            if ($checkingForHasChildren == 1) {
                foreach ( $quote_item->getChildren () as $child ) {
          $totalshipamount= $this->getTotalShipping($child,$quote_item, $quote);
                }
            } else {
                $total = $this->calculateShippingPrice ( $quote_item, $quote );
                $totalshipamount = $totalshipamount + $total;
            }
        }
        $result = Mage::getModel ( 'shipping/rate_result' );
        $show = true;
        if ($show) {
            $method = Mage::getModel ( 'shipping/rate_result_method' );
            $method->setCarrier ( $this->_code );
            $method->setMethod ( $this->_code );
            $method->setCarrierTitle ( $this->getConfigData ( 'title' ) );
            $method->setMethodTitle ( $this->getConfigData ( 'name' ) );
            $method->setPrice ( $totalshipamount );
            $method->setCost ( $totalshipamount );
            $result->append ( $method );
        } else {
            $error = Mage::getModel ( 'shipping/rate_result_error' );
            $error->setCarrier ( $this->_code );
            $error->setCarrierTitle ( $this->getConfigData ( 'name' ) );
            $error->setErrorMessage ( $this->getConfigData ( 'specificerrmsg' ) );
            $result->append ( $error );
        }
        return $result;
    }
    
    /** Function get Total shipping amount */
    public function getTotalShipping($child,$quote_item, $quote){
        $quote_item=$quote_item;
        $quote=$quote;
        if ($child->getFreeShipping () && ! $child->getProduct ()->isVirtual ()) {
            $total = $this->calculateShippingPrice ( $quote_item, $quote );
            return $totalshipamount + $total;
           
        }
        
    }
    
    
    /**
     * Function to get the allowed shipping method data
     *
     * This function will return shipping method data
     *
     * @return array
     */
    public function getAllowedMethods() {
        return array (
                'apptha' => $this->getConfigData ( 'name' ) 
        );
    }
    
    /**
     * Calculate shipping price
     *
     * @param array $quote_item            
     * @param array $quote            
     * @return number $total
     */
    public function calculateShippingPrice($quote_item, $quote) {
        $product = Mage::getModel ( 'catalog/product' )->load ( $quote_item->getProductId () );
        $product->getSellerShippingOption ();
        $nationalShippingPrice = $product->getNationalShippingPrice ();
        $internationalShippingPrice = $product->getInternationalShippingPrice ();
        $sellerDefaultCountry = $product->getDefaultCountry ();
        $shippingCountryId = $quote->getShippingAddress ()->getCountry ();
        $quote->getSubtotal ();
        $qty = $quote_item->getQty ();
        if ($nationalShippingPrice != '' && $internationalShippingPrice != '' && $shippingCountryId != '' && $sellerDefaultCountry == $shippingCountryId) {
            $total = $nationalShippingPrice * $qty;
        } else {
            $total = $internationalShippingPrice * $qty;
        }
        return $total;
    }
    
    /**
     * Checking for virtual or not
     *
     * @param array $quote_item            
     * @return number $checkingForVirtual
     */
    public function checkingForVirtual($quote_item) {
        $checkingForVirtual = 0;
        if ($quote_item->getProduct ()->isVirtual () || $quote_item->getParentItem ()) {
            $checkingForVirtual = 1;
        }
        return $checkingForVirtual;
    }
    /**
     * Checking for has children or not
     *
     * @param array $quote_item            
     * @return number $checkingForHasChildren
     */
    public function checkingForHasChildren($quote_item) {
        $checkingForHasChildren = 0;
        if ($quote_item->getHasChildren () && $quote_item->isShipSeparately ()) {
            $checkingForHasChildren = 1;
        }
        return $checkingForHasChildren;
    }
    
    /**
     * Checking for has children or not
     *
     * @param array $quote_item
     * @return number $checkingForHasChildren
     */
    public function getCarrierStatus() {
        if (! Mage::getStoreConfig ( 'carriers/' . $this->_code . '/active' )) {
            return false;
        }
    }
}