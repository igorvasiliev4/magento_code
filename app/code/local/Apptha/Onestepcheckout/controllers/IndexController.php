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
 * @version     1.2.3
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
/**
 * This will be used for managing one step checkout functions
 * and views in front end
 */
class Apptha_Onestepcheckout_IndexController extends Mage_Core_Controller_Front_Action {
 /**
  * load the onepage template 
  * and check the quotes 
  * if not available redirect to cart page
  */
 public function indexAction() {
  $quote = $this->getOnepage ()->getQuote ();
  /**
   * Check condition
   * if quote variable have value 
   * or have error 
   * if condition is satisfied means return this function
   */
  if (! $quote->hasItems () || $quote->getHasError ()) {
   $this->_redirect ( 'checkout/cart' );
   return;
  }
  
  /**
   * Check condition
   * if not have any value in quote validity minimum amount
   */
  if (! $quote->validateMinimumAmount ()) {
   /**
    * get error value from cofig
    */ 
   $error = Mage::getStoreConfig ( 'sales/minimum_order/error_message' );
   Mage::getSingleton ( 'checkout/session' )->addError ( $error );
   $this->_redirect ( 'checkout/cart' );
   return;
  }
  Mage::getSingleton ( 'checkout/session' )->setCartWasUpdated ( false );
  Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::getUrl ( '*/*/*', array (
    '_secure' => true 
  ) ) );
  /**
   * save billing and shipping information onload
   */
  $helper = Mage::helper ( 'onestepcheckout/checkout' );
  $this->shippingreloadAction ();
  $billing_data = $this->getRequest ()->getPost ( 'billing', array () );
  $customerAddressId = $this->getRequest ()->getPost ( 'billing_address_id', false );
  /**
   * check condition if onepage is visible
   */
  if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
   $Billingdata = $helper->load_add_data ( $billing_data );
   $this->getOnepage ()->saveBilling ( $Billingdata, $customerAddressId );
  } else {
   /**
    * check condition if billing data is not empty
    */
   if (! empty ( $billing_data ['use_for_shipping'] )) {
    $Shippingdata = $helper->load_add_data ( $billing_data );
    $shipping_result1 = $this->getOnepage ()->saveBilling ( $Shippingdata, $customerAddressId );
    $shipping_result = $this->getOnepage ()->saveShipping ( $Shippingdata, $customerAddressId );
   } else {
    /**
     * get shipping address id value from post data
     */
    $shippingAddressId = $this->getRequest ()->getPost ( 'shipping_address_id', false );
    $shipping_data = $this->getRequest ()->getPost ( 'shipping', array () );
    $Shippingdata = $helper->load_add_data ( $shipping_data );
    $shipping_result1 = $this->getOnepage ()->saveBilling ( $Shippingdata, $customerAddressId );
    $shipping_result = $this->getOnepage ()->saveShipping ( $Shippingdata, $shippingAddressId );
   }
  }
  $this->_checkCountry ();
  $this->getOnepage ()->initCheckout ();
  $this->loadLayout ();
  $this->_initLayoutMessages ( 'customer/session' );
  $this->renderLayout ();
 /**
  * End of save billing and shipping information onload
  */
 }
 
 /**
  * End index action method
  */
 
 /**
  * check the billing and shipping country
  *
  * @return void
  */
 private function _checkCountry() {
  $quote = $this->getOnepage ()->getQuote ();
  $shipping = $quote->getShippingAddress ();
  $billing = $quote->getBillingAddress ();
  $helper = Mage::helper ( 'onestepcheckout/checkout' );
  $enableGeoIp = Mage::getStoreConfig ( 'onestepcheckout/general/enable_geoip' );
  $countryId = Mage::getStoreConfig ( 'onestepcheckout/general/default_country_id' );
  /**
   * Check condition
   * enable config value is equal 1
   */
  if (($enableGeoIp == 1) && ($helper->getGeoIp ()->countryCode)){
     $countryId = $helper->getGeoIp ()->countryCode;
  }
  
  /**
   * Check condition
   * if country id is null means get default country 
   * and assign this variable
   */
  if (is_null ( $countryId )) {
   $countryId = Mage::helper ( 'core' )->getDefaultCountry ();
  }
  /**
   * save shipping rate value into database
   */
  $shipping->setCountryId ( $countryId )->setCollectShippingRates ( true )->save ();
  $billing->setCountryId ( $countryId )->save ();
  $shipping->setSameAsBilling ( true )->save ();
 }
 /**
  * End check country method
  */
 
 /**
  * Reload shipping info according 
  * state
  * zip 
  * and country selection
  *
  * @return void
  */
 public function shippingreloadAction() {
  /**
   * check condition if date is expired
   */
  if ($this->_expireAjax ()) {
   return;
  }
  $shipping_methods = $this->getRequest ()->getPost ( 'shipping_method' );
  
  /**
   * Check condition
   * if shipping method is not have any value means load config value
   */
  if (! $shipping_methods) {
   $shipping_methods = Mage::getStoreConfig ( 'onestepcheckout/general/default_shipping_method' );
  }
  $save_shippingmethod = $this->getOnepage ()->saveShippingMethod ( $shipping_methods );
  /**
   * check condition
   * if shipping method is saved or not
   */
  if (! $save_shippingmethod) {
   Mage::dispatchEvent ( 'checkout_controller_onepage_save_shipping_method', array (
     'request' => $this->getRequest (),
     'quote' => $this->getOnepage ()->getQuote () 
   ) );
   $this->getOnepage ()->getQuote ()->collectTotals ();
  }
  $this->getOnepage ()->getQuote ()->collectTotals ()->save ();
  $this->getOnepage ()->getQuote ()->getShippingAddress ()->setShippingMethod ( $shipping_methods );
 }
 
 /**
  * End of index Action
  */
 
 /**
  * if ajax expires check the quouetes
  * if not avaiable redirect to ajaxredirectresponse fn
  */
 protected function _expireAjax() {
  /**
   * get activate cart value from config
   */
  $activateInCart = Mage::getStoreConfig ( 'onestepcheckout/general/Activate_apptha_onestepcheckout_cart' );
  /**
   * check condition activate cart is not equal to 1
   */
  if ($activateInCart != 1) :
   /**
    * check condition quote item is equal to true
    */
   if (! $this->getOnepage ()->getQuote ()->hasItems () || $this->getOnepage ()->getQuote ()->getHasError () || $this->getOnepage ()->getQuote ()->getIsMultiShipping ()) {
    $this->_ajaxRedirectResponse ();
    return true;
   }
   $action = $this->getRequest ()->getActionName ();
   /**
    * check condition cart updated is equal to true
    */
   if (Mage::getSingleton ( 'checkout/session' )->getCartWasUpdated ( true ) && ! in_array ( $action, array (
     'index',
     'progress' 
   ) )) {
    $this->_ajaxRedirectResponse ();
    return true;
   }
  
        endif;
 }
 
 /**
  * End of expireAjax fn
  */
 
 /**
  * set session expires
  * and send the response to Onestepcheckout.js
  */
 public function _ajaxRedirectResponse() {
  $this->getResponse ()->setHeader ( 'HTTP/1.1', '403 Session Expired' )->setHeader ( 'Login-Required', 'true' )->sendResponse ();
  return $this;
 }
 
 /**
  * End of ajaxRedirectResponse fn
  */
 
 /**
  * function:includes the core checkout onepage model
  *
  * @return string
  */
 public function getOnepage() {
  return Mage::getSingleton ( 'checkout/type_onepage' );
 }
 
 /**
  * End of getOnepage fn
  */
 
 /**
  * get the username and password from ajax
  * and check the user table
  * and send the result as json response to js
  */
 public function loginAction() {
  $username = $this->getRequest ()->getPost ( 'onestepcheckout_username', false );
  $password = $this->getRequest ()->getPost ( 'onestepcheckout_password', false );
  $session = Mage::getSingleton ( 'customer/session' );
  
  $result = array (
    'success' => false 
  );
  
  /**
   * check condition username password is not empty
   */
  if ($username && $password) {
   try {
    $session->login ( $username, $password );
   } catch ( Exception $e ) {
    $result ['error'] = $e->getMessage ();
   }
   
   /**
    * check condition error message is not empty
    */
   if (! isset ( $result ['error'] )) {
    $result ['success'] = true;
   }
  } else {
   $result ['error'] = $this->__ ( 'Please enter your Email Id and password.' );
  }
  
  $this->getResponse ()->setBody ( Zend_Json::encode ( $result ) );
 }
 
 /**
  * End of Login Action
  */
 public function forgotPasswordAction() {
  $email = $this->getRequest ()->getPost ( 'email', false );
  
  /**
   * check condition email is not empty
   */
  if (! Zend_Validate::is ( $email, 'EmailAddress' )) {
   $result = array (
     'success' => false 
   );
  } else {
   
   $customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
   
   /**
    * check condition if customer id is not empty
    */
   if ($customer->getId ()) {
    try {
     $newPassword = $customer->generatePassword ();
     $customer->changePassword ( $newPassword, false );
     $customer->sendPasswordReminderEmail ();
     $result = array (
       'success' => true 
     );
    } catch ( Exception $e ) {
     $result = array (
       'success' => false,
       'error' => $e->getMessage () 
     );
    }
   } else {
    $result = array (
      'success' => false,
      'error' => 'notfound' 
    );
   }
  }
  
  $this->getResponse ()->setBody ( Zend_Json::encode ( $result ) );
 }
 
 /**
  * function:load the product information when payment method selects
  */
 public function playAction() {
  /**
   * check condition ajax expire is equal to true
   */
  if ($this->_expireAjax ()) {
   return;
  }
  $this->loadLayout ();
  $this->renderLayout ();
 }
 
 /**
  * End of Play Action
  */
 
 /**
  * function:load the product information when shipping method selects
  */
 public function reloadAction() {
  /**
   * Seller Shipping for Marketplace
   */
  if ($this->_expireAjax ()) {
   return;
  }
  /**
   * get shipping method value from post data
   */
  $shipping_method = $this->getRequest ()->getPost ( 'shipping_method' );
  /**
   * check condition shipping method value is not empty
   */
  if (! $shipping_method) {
   $shipping_method = Mage::getStoreConfig ( 'onestepcheckout/general/default_shipping_method' );
  }
  $save_shippingmethod = $this->getOnepage ()->saveShippingMethod ( $shipping_method );
  /**
   * check condition shipping method data is not empty
   */
  if (! $save_shippingmethod) {
   Mage::dispatchEvent ( 'checkout_controller_onepage_save_shipping_method', array (
     'request' => $this->getRequest (),
     'quote' => $this->getOnepage ()->getQuote () 
   ) );
   $this->getOnepage ()->getQuote ()->collectTotals ();
  }
  $this->getOnepage ()->getQuote ()->collectTotals ()->save ();
  $this->getOnepage ()->getQuote ()->getShippingAddress ()->setShippingMethod ( $shipping_method );
  
  $this->loadLayout ();
  $this->renderLayout ();
 }
 
 /**
  * End of reload Action
  */
 
 /**
  * Start of paymentreload Action
  * payment reload when changes the shipping methods
  */
 public function paymentreloadAction() {
  $this->loadLayout ( false );
  $this->renderLayout ();
 }
 
 /**
  * End of paymentreload Action
  */
 public function summaryAction() {
  /**
   * check condition ajax expire is equal to true
   */
  if ($this->_expireAjax ()) {
   
   return;
  }
  
  $this->loadLayout ();
  $this->renderLayout ();
 }
 /**
  * End summary action
  */
 
 /**
  * ajax save billing function
  * save billing,shipping,payment information
  */
 public function savebillingAction() {
  $billing_data = $this->getRequest ()->getPost ( 'billing', array () );
  $shipping_data = $this->getRequest ()->getPost ( 'shipping', array () );
   $customerAddressId = $this->getRequest ()->getPost ( 'billing_address_id', false );
   $shippingAddressId = $this->getRequest ()->getPost ( 'shipping_address_id', false );
  if (isset ( $billing_data ['use_for_shipping'] ) == 1) {
   $billingCountryId = $billing_data ['country_id'];
   $billingRegionId = $billing_data ['region_id'];
   $billingZipcode = $billing_data ['postcode'];
   $billingRegion = $billing_data ['region'];
   $billingCity = $billing_data ['city'];
   $this->getOnepage ()->getQuote ()->getBillingAddress ()->setCountryId ( $billingCountryId )->setRegionId ( $billingRegionId )->setPostcode ( $billingZipcode )->setRegion ( $billingRegion )->setCity ( $billingCity )->setCollectShippingRates ( true );
   $this->getOnepage ()->getQuote ()->getShippingAddress ()->collectTotals ();
   $this->getOnepage ()->getQuote ()->save ();
  } else {
    $this->getShippingInfo($shipping_data);
  }
   if ((Mage::helper ( 'customer' )->isLoggedIn ()) && (! empty ( $customerAddressId ))) {
   $billingAddress = Mage::getModel ( 'customer/address' )->load ( $customerAddressId );
    if (is_object ( $billingAddress ) && $billingAddress->getCustomerId () == Mage::helper ( 'customer' )->getCustomer ()->getId ()) {
    $billing_data = array_merge ( $billing_data, $billingAddress->getData () );
   }
  }
   $config = Mage::getStoreConfig ( 'tax/calculation/based_on' );
  $helper = Mage::helper ( 'onestepcheckout/checkout' );
  if ($config == "billing") {
   $billing_info = $helper->load_add_data ( $billing_data );
   $billing_result = $this->getOnepage ()->saveBilling ( $billing_info, $customerAddressId );
  } else {
  if (! empty ( $billing_data ['use_for_shipping'] )) {
    $Billingdata = $helper->load_add_data ( $billing_data );
    $shipping_result = $this->getOnepage ()->saveShipping ( $Billingdata, $customerAddressId );
   } else {
   if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
     $billing_info = $helper->load_add_data ( $billing_data );
     $billing_result = $this->getOnepage ()->saveBilling ( $billing_info, $customerAddressId );
    } else {
     $shipping_info = $helper->load_add_data ( $shipping_data );
     $shipping_result = $this->getOnepage ()->saveShipping ( $shipping_info, $shippingAddressId );
    }
   }
  }
  if (! empty ( $billing_data ['use_for_shipping'] )) {
   if (! empty ( $billing_data ['country_id'] )) {
    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCountryId ( $billing_data ['country_id'] )->setCollectShippingRates ( true );
   }
 if (! empty ( $billing_data ['region_id'] )) {
    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setRegionId ( $billing_data ['region_id'] )->setCollectShippingRates ( true );
   }
   if (! empty ( $billing_data ['region'] )) {
     $this->getOnepage ()->getQuote ()->getShippingAddress ()->setRegionId ( $billing_data ['region'] )->setCollectShippingRates ( true );
   }
    if (! empty ( $billing_data ['city'] )) {
    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCity ( $billing_data ['city'] )->setCollectShippingRates ( true );
   }
   if (! empty ( $billing_data ['postcode'] )) {
    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setPostcode ( $billing_data ['postcode'] )->setCollectShippingRates ( true );
   }
  } else {
  if (isset( $shipping_data ['country_id'] )) {
    if (!empty($shipping_data ['country_id'] )) {
     $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCountryId ( $shipping_data ['country_id'] )->setCollectShippingRates ( true );
    } else {
     $this->getOnepage ()->getQuote ()->getBillingAddress ()->setCountryId ( $shipping_data ['country_id'] )->setCollectShippingRates ( true );
    }
   }
    if (!empty( $shipping_data ['region_id'] )) {
    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setRegionId ( $shipping_data ['region_id'] )->setCollectShippingRates ( true );
   }
  if (!empty($shipping_data ['region'] )) {    
    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setRegionId ( $shipping_data ['region'] )->setCollectShippingRates ( true );
   }
  if (!empty($shipping_data ['city'] )) {
    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCity ( $shipping_data ['city'] )->setCollectShippingRates ( true );
   }
    if (!empty($shipping_data ['postcode'] )) {
    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setPostcode ( $shipping_data ['postcode'] )->setCollectShippingRates ( true );
   }
  }
   $paymentMethod = $this->getRequest ()->getPost ( 'payment_method', false );
  if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
   $this->getOnepage ()->getQuote ()->getBillingAddress ()->setPaymentMethod ( ! empty ( $paymentMethod ) ? $paymentMethod : null );
  } else {
   $this->getOnepage ()->getQuote ()->getShippingAddress ()->setPaymentMethod ( ! empty ( $paymentMethod ) ? $paymentMethod : null );
  }
   $this->loadLayout ( false );
  $this->renderLayout ();
 }
 
 
 public function getShippingInfo($shipping_data){
     $shippingCountryId = $shippingRegionId = $shippingZipcode = $shippingRegion = $shippingCity = '';
     if (isset ( $shipping_data ['country_id'] )) {
         $shippingCountryId = $shipping_data ['country_id'];
     }
     if (isset ( $shipping_data ['region_id'] )) {
         $shippingRegionId = $shipping_data ['region_id'];
     }
     if (isset ( $shipping_data ['postcode'] )) {
         $shippingZipcode = $shipping_data ['postcode'];
     }
     if (isset ( $shipping_data ['region'] )) {
         $shippingRegion = $shipping_data ['region'];
     }
     if (isset ( $shipping_data ['city'] )) {
         $shippingCity = $shipping_data ['city'];
     }
     $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCountryId ( $shippingCountryId )->setRegionId ( $shippingRegionId )->setPostcode ( $shippingZipcode )->setRegion ( $shippingRegion )->setCity ( $shippingCity )->setCollectShippingRates ( true );
     $this->getOnepage ()->getQuote ()->getShippingAddress ()->collectTotals ();
     $this->getOnepage ()->getQuote ()->save ();
     
 }
 
 
 
 protected function _getReviewHtml() {
  return $this->getLayout ()->createBlock ( 'paypal/iframe' )->toHtml ();
 }
 
 public function saveOrderAction() {
  
  if ($this->_expireAjax ()) {
   return;
  }
  
  $helper = Mage::helper ( 'onestepcheckout/checkout' );
  
  if ($this->getRequest ()->isPost ()) {
   $Method = $this->getRequest ()->getPost ( 'checkout_method', false );
   $Billingdata = $this->getRequest ()->getPost ( 'billing', array () );
   $Billingdata = $helper->load_exclude_data ( $Billingdata );
   $Paymentdata = $this->getRequest ()->getPost ( 'payment', array () );
   $result = $this->getOnepage ()->saveCheckoutMethod ( $Method );
  
   if (isset ( $Billingdata ['is_subscribed'] ) && ! empty ( $Billingdata ['is_subscribed'] )) {
    $this->getOnepage ()->getCheckout ()->setCustomerIsSubscribed ( 1 );
   }
   $customerAddressId = $this->getRequest ()->getPost ( 'billing_address_id', false );
   if (isset ( $Billingdata ['email'] )) {
    $Billingdata ['email'] = trim ( $Billingdata ['email'] );
   }
  
   $Billingresult = $this->getOnepage ()->saveBilling ( $Billingdata, $customerAddressId );
   
   if (! empty ( $Billingresult )) {
    $result ['error'] = true;
    $result ['error_messages'] = $Billingresult ['message'];
    $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
    return;
   }
   
   $Paymentresult = $this->getOnepage ()->savePayment ( $Paymentdata );
  
   if (! empty ( $Paymentresult )) {
    $result ['error'] = true;
    $result ['error_messages'] = $Paymentresult ['message'];
    $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
    return;
   }
  
   if (isset ( $Billingdata ['email'] )) {
   
    if (! Zend_Validate::is ( $Billingdata ['email'], 'EmailAddress' )) {
     $result ['error'] = true;
     $result ['error_messages'] = $this->__ ( 'Invalid Email address' );
     $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
     return;
    }
   
    if ($Method == 'register') {
     $cust_exist = Mage::helper ( 'onestepcheckout/checkout' )->IscustomerEmailExists ( $Billingdata ['email'] );
     
     if ($cust_exist) {
      $result ['error'] = true;
      $result ['error_messages'] = $this->__ ( 'Email address Already Exists' );
      $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
      return;
     }
    }
   }
   $Shippingdata = $this->getRequest ()->getPost ( 'shipping', array () );
   $ShippingAddressId = $this->getRequest ()->getPost ( 'shipping_address_id', false );
    if (! empty ( $Billingdata ['use_for_shipping'] )) {
    $shipping_result = $this->getOnepage ()->saveShipping ( $Billingdata, $customerAddressId );
   } else if (! empty ( $ShippingAddressId )) {
$shippingAddress = Mage::getModel ( 'customer/address' )->load ( $ShippingAddressId );
  
    if (is_object ( $shippingAddress ) && $shippingAddress->getCustomerId () == Mage::helper ( 'customer' )->getCustomer ()->getId ()) {
     $Shippingdata = array_merge ( $Shippingdata, $shippingAddress->getData () );
     $shipping_result = $this->getOnepage ()->saveShipping ( $Shippingdata, $ShippingAddressId );
    }
   } else if (empty ( $Billingdata ['use_for_shipping'] ) && ! $ShippingAddressId) {
   
    $shipping_result = $this->getOnepage ()->saveShipping ( $Shippingdata, $ShippingAddressId );
   } else {
    $shipping_result = $this->getOnepage ()->saveShipping ( $Billingdata, $customerAddressId );
   }
   $ShippingMethoddata = $this->getRequest ()->getPost ( 'shipping_method', '' );
   $this->getOnepage ()->saveShippingMethod ( $ShippingMethoddata );
  }
  Mage::dispatchEvent ( 'checkout_controller_onepage_save_shipping_method', array (
    'request' => $this->getRequest (),
    'quote' => $this->getOnepage ()->getQuote () 
  ) );
  $data = $this->getRequest ()->getPost ( 'payment', array () );
  $result = $this->getOnepage ()->savePayment ( $data );
 $redirectUrl = $this->getOnepage ()->getQuote ()->getPayment ()->getCheckoutRedirectUrl ();
if ($redirectUrl) {
 if ($requiredAgreements = Mage::helper ( 'checkout' )->getRequiredAgreementIds ()) {
    $postedAgreementsArray = array_keys ( $this->getRequest ()->getPost ( 'agreement', array () ) );
 if ($diff = array_diff ( $requiredAgreements, $postedAgreementsArray )) {
     $result ['success'] = false;
     $result ['error'] = true;
     $result ['error_messages'] = $this->__ ('Please agree to all the terms and conditions before placing the order.' );
     $this->getResponse ()->setBody(Mage::helper ( 'core' )->jsonEncode ( $result ) );
     return;
    }
   }
   $result ['success'] = true;
   $result ['error'] = false;
   $result ['redirect'] = $redirectUrl;
   $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
   return;
  }
  $result = array ();
  try {
  if ($requiredAgreements = Mage::helper ( 'checkout' )->getRequiredAgreementIds ()) {
    $postedAgreements = array_keys ( $this->getRequest ()->getPost ( 'agreement', array () ) );
  if ($diff = array_diff ( $requiredAgreements, $postedAgreements )) {
     $result ['success'] = false;
     $result ['error'] = true;
     $result ['error_messages'] = $this->__ ( 'Please agree to all the terms and conditions before placing the order.' );
     $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
     return;
    }
   }
   if ($data = $this->getRequest ()->getPost ( 'payment', false )) {
    $this->getOnepage ()->getQuote ()->getPayment ()->importData ( $data );
   }
   $this->getOnepage ()->saveOrder ();
   $redirectUrl = $this->getOnepage ()->getCheckout ()->getRedirectUrl ();
   $result ['success'] = true;
   $result ['error'] = false;
  } catch ( Mage_Payment_Model_Info_Exception $e ) {
   $message = $e->getMessage ();
   if (! empty ( $message )) {
    $result ['error_messages'] = $message;
   }
  } catch ( Mage_Core_Exception $e ) {
   Mage::logException ( $e );
   $result ['success'] = false;
   $result ['error'] = true;
   $result ['error_messages'] = $e->getMessage ();
   Mage::helper ( 'checkout' )->sendPaymentFailedEmail ( $this->getOnepage ()->getQuote (), $e->getMessage () );
   $result ['success'] = false;
   $result ['error'] = true;
   $result ['error_messages'] = $e->getMessage ();
  } catch ( Exception $e ) {
   Mage::logException ( $e );
   $result ['success'] = false;
   $result ['error'] = true;
   $result ['error_messages'] = $e->getMessage ();
   Mage::helper ( 'checkout' )->sendPaymentFailedEmail ( $this->getOnepage ()->getQuote (), $e->getMessage () );
   $result ['success'] = false;
   $result ['error'] = true;
   $result ['error_messages'] = $this->__ ( 'There was an error processing your order. Please contact us or try again later.' );
  }
  
  $this->getOnepage ()->getQuote ()->save ();
 if (isset ( $redirectUrl )) {
   $result ['redirect'] = $redirectUrl;
  }
 if ((isset ( $Paymentdata ['method'] )) && ($Paymentdata ['method'] == 'hosted_pro' || $Paymentdata ['method'] == 'payflow_advanced')) {
   $this->loadLayout ( 'onestepcheckout_index_review' );
   $result ['update_section'] = array (
     'name' => 'paypaliframe',
     'html' => $this->_getReviewHtml () 
   );
  }
  if ($result ['success']) {
   $result ['success'] = Mage::getBaseUrl().'checkout/onepage/success/';
  }
$this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
 }
 

 function couponcodeAction() {
  $quote = $this->getOnepage ()->getQuote ();
  $couponCode = ( string ) $this->getRequest ()->getParam ( 'code' );
  
  /**
   * check condition post remove data is equal to 1
   */
  if ($this->getRequest ()->getParam ( 'remove' ) == 1) {
   $couponCode = '';
  }
   $response = array (
    'success' => false,
    'error' => false,
    'message' => false 
  );
  try {
   $quote->getShippingAddress ()->setCollectShippingRates ( true );
   $quote->setCouponCode ( strlen ( $couponCode ) ? $couponCode : '' )->collectTotals ()->save ();
   
   /**
    * check condition coupon code is not empty
    */
   if ($couponCode) {
    /**
     * check condition coupon code equal to quote coupon code value
     */
    if ($couponCode == $quote->getCouponCode ()) {
     $response ['success'] = true;
     $response ['message'] = $this->__ ( 'Coupon code "%s" was applied successfully.', Mage::helper ( 'core' )->htmlEscape ( $couponCode ) );
    } else {
     $response ['success'] = false;
     $response ['error'] = true;
     $response ['message'] = $this->__ ( 'Coupon code "%s" is not valid.', Mage::helper ( 'core' )->htmlEscape ( $couponCode ) );
    }
   } else {
    $response ['success'] = true;
    $response ['message'] = $this->__ ( 'Coupon code was canceled successfully.' );
   }
  } catch ( Mage_Core_Exception $e ) {
   $response ['success'] = false;
   $response ['error'] = true;
   $response ['message'] = $e->getMessage ();
  } catch ( Exception $e ) {
   $response ['success'] = false;
   $response ['error'] = true;
   $response ['message'] = $this->__ ( 'Can not apply coupon code.' );
  }
  $html = $this->getLayout ()->createBlock ( 'onestepcheckout/onestep_review_info' )->setTemplate ( 'onestepcheckout/onestep/review/info.phtml' )->toHtml ();
  $response ['summary'] = $html;
  $this->getResponse ()->setBody ( Zend_Json::encode ( $response ) );
 }
 /**
  * End of couponcode Action
  */
 
 /**
  * Load reply action and render layout files
  */
 public function replayAction() {
  /**
   * check condition ajax is expire
   */
  if ($this->_expireAjax ()) {
   
   return;
  }
$quote = $this->getOnepage ()->getQuote ();
  /**
   * check condition shipping address is not equal to empty
   */
  if ($quote->getShippingAddress ()) {
   $quote->getShippingAddress ()->setCollectShippingRates ( true );
   $quote->collectTotals ()->save ();
  }
 $this->loadLayout ();
  $this->renderLayout ();
 }
}