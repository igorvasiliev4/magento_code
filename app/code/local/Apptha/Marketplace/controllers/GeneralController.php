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
 * @version     0.1.7
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
/**
 * This file contains General functionality
 */
class Apptha_Marketplace_GeneralController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Function to display change buyer into seller form
     *
     * change buyer in to seller form
     *
     * @return void
     */
    function changebuyerAction() {
    	/** Check MarketplaceKey */
         Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
         /** To check customer logged in or not */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'customer/account/login' );
            return;
        }else{
        	/** Getting customer status */
        	$customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
        	/** Getting Group id */
        	$getGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        	/** Get Customer */
        	$customer = Mage::getSingleton ( "customer/session" )->getCustomer ();
        	/** Getting Customer Group Id */
        	$customerGroupId = $customer->getGroupId();        	
        	if($customerStatus == 1 && $getGroupId == $customerGroupId){
        		$this->_redirect ( 'marketplace/seller/dashboard' );
        		return true;
        	}       	
        }
        /** To load and render Layout */
             
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function to change buyer into seller
     *
     * convert and change group id from buyer into seller
     *
     * @return void
     */
    function becomesellerAction() {
    	/** Getting store config for admin approval */
      $adminApproval = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/need_approval' );
        $approval = 0;
        if ($adminApproval == 1) {
            $approval = 0;
        } else {
            $approval = 1;
        }
        /** Getting group id */
        $getGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        /** Get Customer */
        $customer = Mage::getSingleton ( "customer/session" )->getCustomer ();
        /** Save Group Id */
        $customer->setGroupId ( $getGroupId )->save ();
        /** getting customer Id */
        $customerId = $customer->getId ();
        /** To load based on customer id */
        $model = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        /** Approval save */
        $model->setCustomerstatus ( $approval )->save ();
        Mage::getModel ( 'marketplace/sellerprofile' )->adminApproval ( $customerId );
        /** Setting Session Message */
        if ($adminApproval == 1) {
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
        } else {
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Thank you for registering with %s.', Mage::app ()->getStore ()->getFrontendName () ) );
        }
        $this->_redirect ( 'customer/account' );
    }
    
    /**
     * Function to display vacation mode to seller
     *
     * Display vacation mode page
     *
     * @return void
     */
    function vacationmodeAction() {
    	/** To load and render layout */
        $this->loadLayout ();
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Vacation Mode' ) );
        $this->renderLayout ();
    }
    
    /**
     * Function to save vacation mode to seller
     *
     * Display vacation mode save page
     *
     * @return void
     */
    function vacationmodesaveAction() {
        $vacationStatus = $this->getRequest ()->getParam ( 'vacation_status' );
        $vacationMessage = $this->getRequest ()->getParam ( 'vacation_message' );
        $disableProducts = $this->getRequest ()->getParam ( 'disable_products' );
        $dateFrom = $this->getRequest ()->getParam ( 'date_from' );
        $dateTo = $this->getRequest ()->getParam ( 'date_to' );
        $currentDate = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
        
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            
            $seller = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $sellerId = $seller->getId ();
            
            $product = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $sellerId );
            $productId = array ();
            foreach ( $product as $_product ) {
                $productId [] = $_product->getId ();
            }
            
            $sellerInfo = Mage::getModel ( 'marketplace/vacationmode' )->load ( $sellerId, 'seller_id' );
            $getId = $sellerInfo->getId ();
            
            if ($getId) {
                $updateExisting = Mage::getModel ( 'marketplace/vacationmode' )->load ( $sellerId, 'seller_id' );
                $updateExisting->setVacationMessage ( $vacationMessage );
                $updateExisting->setVacationStatus ( $vacationStatus );
                if (strtotime ( $dateTo ) >= strtotime ( $currentDate )) {
                    $updateExisting->setProductDisabled ( $disableProducts );
                }
                $updateExisting->setDateFrom ( $dateFrom );
                $updateExisting->setDateTo ( $dateTo );
                $updateExisting->setSellerId ( $sellerId );
                $updateExisting->save ();
            } else {
                $insertNew = Mage::getModel ( 'marketplace/vacationmode' );
                $insertNew->setVacationMessage ( $vacationMessage );
                $insertNew->setVacationStatus ( $vacationStatus );
                if (strtotime ( $dateTo ) >= strtotime ( $currentDate )) {
                    $insertNew->setProductDisabled ( $disableProducts );
                }
                $insertNew->setDateFrom ( $dateFrom );
                $insertNew->setDateTo ( $dateTo );
                $insertNew->setSellerId ( $sellerId );
                $insertNew->save ();
            }
            Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
            
            Mage::helper ( 'marketplace/product' )->changevacationmode ( $vacationStatus, $disableProducts, $productId, $dateTo, $currentDate );
            
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your vacation mode information is saved successfully' ) );
            
            $this->_redirect ( 'marketplace/general/vacationmode' );
            return true;
        }
    }
}