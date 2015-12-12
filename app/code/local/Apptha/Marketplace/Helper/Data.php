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
 * @version     1.7
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
class Apptha_Marketplace_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /**
     * Function to get seller group id
     *
     * Return seller group id
     *
     * @return int
     */
    public function getGroupId() {
        $cGroup = Mage::getModel ( 'customer/group' )->load ( 'marketseller', 'customer_group_code' );
        return $cGroup->getCustomerGroupId ();
    }
    /**
     * Getting selected prdouct types
     *
     * Return selected product types
     *
     * @return int
     */
    public function getSelectedPrdouctType() {
        return Mage::getStoreConfig ( 'marketplace/product/producttype' );
    }
    /**
     * Getting product custom option configuration
     *
     * Return product custom option enable or not
     *
     * @return int
     */
    
    public function getPrdouctCustomOptions() {
        return Mage::getStoreConfig ( 'marketplace/product/productcustomoptions' );
    }
    /**
     * Getting product approval option configuration
     *
     * Return product approval enable or not
     *
     * @return int
     */
    
    public function getProductApproval() {
        return Mage::getStoreConfig ( 'marketplace/product/productapproval' );
    }
    /**
     * Getting product types
     *
     * Return enabled product types
     *
     * @return int
     */
    
    public function getProductTypes() {
        return array (
                "simple" => "Simple Product",
                "virtual" => "Virtual Product",
                "downloadable" => "Downloadable Product",
                "configurable" => "Configurable Product" 
        );
    }
    /**
     * License key
     *
     * Return license key given or not
     *
     * @return int
     */
    
    public function checkMarketplaceKey() {
        $apikey = Mage::getStoreConfig ( 'marketplace/marketplace/apply_apptha_licensekey' );
        $marketplaceApiKey = $this->marketplaceApiKey ();
        if ($apikey != $marketplaceApiKey) {
            $keyerror = base64_decode ( 'PGgzIHN0eWxlPSJmbG9hdDpsZWZ0O2NvbG9yOnJlZDttYXJnaW46IDMwMHB4IDQxMHB4OyB3aWR0aDoyNiU7IHRleHQtZGVjb3JhdGlvbjp1bmRlcmxpbmU7IiBpZD0idGl0bGUtdGV4dCI+PGEgdGFyZ2V0PSJfYmxhbmsiIGhyZWY9Imh0dHA6Ly93d3cuYXBwdGhhLmNvbS9jaGVja291dC9jYXJ0L2FkZC9wcm9kdWN0LzE1NiIgc3R5bGU9ImNvbG9yOnJlZDsiPkludmFsaWQgTGljZW5zZSBLZXkgLSBCdXkgbm93PC9hPjwvaDM+' );
            die($keyerror );
        }
    }
    /**
     * Function to get the license key
     *
     * Return generated license key
     *
     * @return string
     */
    
    public function marketplaceApiKey() {
        $code = $this->genenrateOscdomain ();
        return substr ( $code, 0, 25 ) . "CONTUS";
    }
    /**
     * Function to get the domain key
     *
     * Return domain key
     *
     * @return string
     */
    
    public function domainKey($tkey) {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $stringLength = strlen ( $tkey );
        for($i = 0; $i < $stringLength; $i ++) {
            $keyArray [] = $tkey [$i];
        }
        $encMessage = "";
        $kPos = 0;
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen ( $charsStr );
        for($i = 0; $i < $strLen; $i ++) {
            $charsArray [] = $charsStr [$i];
        }
        $lenMessage = strlen ( $message );
        $count = count ( $keyArray );
        for($i = 0; $i < $lenMessage; $i ++) {
            $char = substr ( $message, $i, 1 );
            $offset = $this->getOffset ( $keyArray [$kPos], $char );
            $encMessage .= $charsArray [$offset];
            $kPos ++;
            
            if ($kPos >= $count) {
                $kPos = 0;
            }
        }
        return $encMessage;
    }
    /**
     * Function to get the offset for license key
     *
     * Return offset key
     *
     * @return string
     */
    
    public function getOffset($start, $end) {
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen ( $charsStr );
        for($i = 0; $i < $strLen; $i ++) {
            $charsArray [] = $charsStr [$i];
        }
        for($i = count ( $charsArray ) - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $charsArray [$i] )] = $i;
        }
        $sNum = $lookupObj [ord ( $start )];
        $eNum = $lookupObj [ord ( $end )];
        $offset = $eNum - $sNum;
        if ($offset < 0) {
            $offset = count ( $charsArray ) + ($offset);
        }
        return $offset;
    }
    /**
     * Function to generate license key
     *
     * Return license key
     *
     * @return string
     */
    
    public function genenrateOscdomain() {
        $subfolder = $matches = '';
        $strDomainName = Mage::app ()->getFrontController ()->getRequest ()->getHttpHost ();
        preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder [2], $matches );
        if (isset ( $matches ['domain'] )) {
            $clientUrl = $matches ['domain'];
        } else {
            $clientUrl = "";
        }
        $clientUrl = str_replace ( "www.", "", $clientUrl );
        $clientUrl = str_replace ( ".", "D", $clientUrl );
        $clientUrl = strtoupper ( $clientUrl );
        if (isset ( $matches ['domain'] )) {
            $response = $this->domainKey ( $clientUrl );
        } else {
            $response = "";
        }
        return $response;
    }
} 
 