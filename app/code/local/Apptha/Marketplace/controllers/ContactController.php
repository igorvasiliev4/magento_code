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
 * This file is used for contact admin functionality
 */
class Apptha_Marketplace_ContactController extends Mage_Core_Controller_Front_Action {

 /**
  * Load phtml file layout
  *
  * @return void
  */
 public function indexAction() {
  /**
   * check marketplace key
   */
  Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
  /**
   * check condition customer is logged in
   */
  if (! $this->_getSession ()->isLoggedIn ()) {
   Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
   $this->_redirect ( '*/*/login' );
   return;
  }
  /**
   * To load and render Layout 
   */
  $this->loadLayout ();
  $this->renderLayout ();
 }
 /**
  * Load contact admin form
  *
  * @return void
  */
 public function formAction() {
 	/** 
 	 * check customer and seller are equal 
 	 */
  $customerStatus = $this->checkSellerCustomerAreEqual ();
  /**
   * Checking whether customer approved or not
   */
  if ($customerStatus != 1) {
  	/**
  	 * Add error Message
  	 *  */
   Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
   $this->_redirect ( 'marketplace/seller/login' );
   return;
  }
  /**
   * To load and render layout
   */
  $this->loadLayout ();
  $this->renderLayout ();
 }
 /**
  * Send email to admin
  *
  * @return void
  */
 public function postAction() {
 	/** to check customer and seller are equal */
  $customerStatus = $this->checkSellerCustomerAreEqual ();
  /**
   * Checking whether customer approved or not
   */
  if ($customerStatus != 1) {
   Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
   /** redirect url */
   $this->_redirect ( 'marketplace/seller/login' );
   return;
  }
  /**
   * check condition seller registration is equal to 1
   */
  if (Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/contact_admin' ) == 1) {
   $subject = $message = '';
   /** get subject */
   $subject = $this->getRequest ()->getPost ( 'subject' );
   /** get message */
   $message = $this->getRequest ()->getPost ( 'message' );
   /**
    * check condition subject is not empty and message is not empty
    */
   if (! empty ( $subject ) && ! empty ( $message )) {
    /**
     * Sending email to admin
     */
    try {
     $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/contact_email_template_selection' );
     /** getting admin email id */
     $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
     $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
     /** getting name */
     $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
     /**
      * Selecting template id
      */     
     $emailTemplate = Mage::helper('marketplace/general')->getSellerApprovalEmailTemplate($templateId,4);     
      $sellerId = Mage::getSingleton ( 'customer/session' )->getId ();
     $customer = Mage::getModel ( 'customer/customer' )->load ( $sellerId );
   $seller_info = Mage::getModel ( 'marketplace/sellerprofile' )->load ( $sellerId, 'seller_id' );
    $selleremail = $customer->getEmail ();
     $recipient = $toMailId;
      $sellername = $customer->getName ();
    $contactno = $seller_info ['contact'];
    $emailTemplate->setSenderName ( $sellername );
     $emailTemplate->setSenderEmail ( $selleremail );
     $emailTemplateVariables = (array (
       'ownername' => $toName,
       'sellername' => $sellername,
       'selleremail' => $selleremail,
       'subject' => $subject,
       'message' => $message,
       'contactno' => $contactno 
     ));
     $emailTemplate->setDesignConfig ( array (
       'area' => 'frontend' 
     ) );
 $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
 $emailTemplate->send ( $recipient, $sellername, $emailTemplateVariables );
 Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.' ) );
 $this->_redirect ( '*/*/form' );
    } catch ( Mage_Core_Exception $e ) {
    
     Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
     $this->_redirect ( '*/*/form' );
    } catch ( Exception $e ) {
    
     Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
     $this->_redirect ( '*/*/form' );
    }
   } else {
    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please enter all required fields' ) );
    $this->_redirect ( '*/*/form' );
   }
  }
 }
 
 /**
  * Getting customer
  *
  * @return string
  */
 protected function _getSession() {
 	/** getting customer session */
  return Mage::getSingleton ( 'customer/session' );
 }

 /**
  * Check customer and seller are equal
  *
  * @return int
  */
 public function checkSellerCustomerAreEqual() {
  /**
   * Initilize customer and seller group id
   */
  $customerGroupId = $sellerGroupId = $customerStatus = '';
  /**
   * get customer groupid
   */
  $customerGroupId = Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
  /**
   * get group id
   */
  $sellerGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
  /**
   * get customer status
   */
  $customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
  /**
   * check condition customer is logged in and customer group id not equal to seller group id
   */
  if (! $this->_getSession ()->isLoggedIn () && $customerGroupId != $sellerGroupId) {
  	/** 
  	 * Add error message
  	 */
   Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
   $this->_redirect ( 'marketplace/seller/login' );
  }
  /**
   * return customer status
   */
  return $customerStatus;
 }
}