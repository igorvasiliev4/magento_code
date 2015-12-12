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
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */

/**
 * This file is used to manage order information
 */
class Apptha_Marketplace_OrderController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }
    /**
     * Load phtml layout file to display order information
     * 
     * @return void
     */
    public function indexAction() {
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Manage orders by sellers
     * 
     * @return void
     */
    public function manageAction() {
        Mage::helper('marketplace')->checkMarketplaceKey();
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
     /**
      * View full order information by seller
      * 
      * @return void
      */
     public function vieworderAction(){
        	/**
    	 * check license key
    	 */
    	Mage::helper('marketplace')->checkMarketplaceKey();
    	
    	/**
    	 *  Initilize customer and seller group id
    	*/
    	$customerGroupId = $sellerGroupId = $customerStatus = '';
    	$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
    	$sellerGroupId = Mage::helper('marketplace')->getGroupId();
    	$customerStatus = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
    	
    	if (!Mage::getSingleton('customer/session')->isLoggedIn() && $customerGroupId != $sellerGroupId) {
    		Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
    		$this->_redirect('marketplace/seller/login');
    		return false;
    	}
    	/**
    	 *  Checking whether customer approved or not
    	 */
    	if ($customerStatus != 1) {
    		Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
    		$this->_redirect('marketplace/seller/login');
    		return false;
    	}	
    	
    	$orderId = $this->getRequest()->getParam('orderid');
    	
    	$orderPrdouctIds = Mage::helper('marketplace/vieworder')->getOrderProductIds(Mage::getSingleton('customer/session')->getId(),$orderId);
    	if(count($orderPrdouctIds) <= 0){
    	$this->_redirect('marketplace/order/manage');
    	return false;
    	}

    	$collection = Mage::getModel('marketplace/commission')->getCollection()
    	->addFieldToFilter('seller_id',Mage::getSingleton('customer/session')->getId())
    	->addFieldToFilter('order_id',$orderId)    
    	->getFirstItem();
    	
    	if(count($collection) >=1 && $collection->getOrderId() == $orderId){       
          $this->loadLayout();
          $this->renderLayout();  
    	  }else{
    	 Mage::getSingleton('core/session')->addError($this->__('You do not have permission to access this page'));
    	 $this->_redirect('marketplace/order/manage');
    	 return false;
         } 
      }
     /**
      * View full transaction history by seller
      * 
      * @return void
      */
      function viewtransactionAction(){
        Mage::helper('marketplace')->checkMarketplaceKey();
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
          $this->loadLayout();
          $this->renderLayout();  
      }
       /**
        * Seller payment acknowledgement
        * 
        * @return void
        */
      function acknowledgeAction(){
        Mage::helper('marketplace')->checkMarketplaceKey();
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        } 
          $this->loadLayout();
          $this->renderLayout();
          $commissionId = $this->getRequest()->getParam('commissionid');        
          if($commissionId!=''){
          $collection = Mage::getModel('marketplace/transaction')->changeStatus($commissionId);          
          if($collection==1){
              Mage::getSingleton('core/session')->addSuccess($this->__("Payment received status has been updated")); 
              $this->_redirect('marketplace/order/viewtransaction');
          } else  {
             Mage::getSingleton('core/session')->addError($this->__('Payment received status was not updated'));
             $this->_redirect('marketplace/order/viewtransaction'); 
          }
      }
   }
	/**
     * customer order cancel request
     * 
     * @return void
     */   
   	public function cancelAction(){   		
   		$orderCancelStatusFlag = Mage::getStoreConfig('marketplace/admin_approval_seller_registration/order_cancel_request');
   		$data = $this->getRequest()->getPost(); 
   		$emailSent = '';
   		
   		$orderId = $data['order_id'];   		
   		$loggedInCustomerId = '';
   		if(Mage::getSingleton('customer/session')->isLoggedIn() && isset($orderId)) {
   			$customerData = Mage::getSingleton('customer/session')->getCustomer();
   			$loggedInCustomerId = $customerData->getId();
   			$customerid = Mage::getModel('sales/order')->load($data['order_id'])->getCustomerId();   		
   		}else{
   			Mage::getSingleton('core/session')->addError($this->__("You do not have permission to access this page"));
   			$this->_redirect('sales/order/history');
   			return;
   		}   		
   		if($orderCancelStatusFlag == 1 && !empty($loggedInCustomerId) && $customerid == $loggedInCustomerId){ 				
   		
   			$shippingStatus = 0;
   			    try {
                $templateId = (int) Mage::getStoreConfig('marketplace/admin_approval_seller_registration/order_cancel_request_notification_template_selection');
          
                if ($templateId) {
                    $emailTemplate = Mage::helper('marketplace/marketplace')->loadEmailTemplate($templateId);
                } else {
                    $emailTemplate = Mage::getModel('core/email_template')
                            ->loadDefault('marketplace_cancel_order_admin_email_template_selection');
                }                
                
                $_order = Mage::getModel('sales/order')->load($orderId);
                $incrementId = $_order->getIncrementId();
                $sellerProductDetails = array();
                $selectedProducts = $data['products'];                
                $selectedItemproductId = '';
                
                foreach($_order->getAllItems() as $item){
                $itemProductId = $item->getProductId();
                $orderItem = $item;                  
                if(in_array($itemProductId,$selectedProducts)){
                if( $orderItem->getQtyShipped() <  $orderItem->getQtyOrdered() && $orderItem->getIsVirtual() != 1){
                $shippingStatus = 1;
                }	
                $sellerId = Mage::getModel('catalog/product')->load($itemProductId)->getSellerId();
                $selectedItemproductId = $itemProductId; 
                $sellerProductDetails[$sellerId][] = $item->getName();
                }
                }                

                foreach($sellerProductDetails as $key => $productDetails){
                $productDetailsHtml = "<ul>";
                foreach($productDetails as $productDetail){
                $productDetailsHtml .= "<li>";
                $productDetailsHtml .= $productDetail;
                $productDetailsHtml .= "</li>";                	
                }	
                $productDetailsHtml .= "</ul>";                 
            
                $customer = Mage::getModel('customer/customer')->load($loggedInCustomerId);
                $seller = Mage::getModel('customer/customer')->load($key);
                
                $buyerName = $customer->getName();
                $buyerEmail = $customer->getEmail();              

                $sellerEmail = $seller->getEmail();
                $sellerName = $seller->getName();
                
                $recipient = $sellerEmail;
        
                $sellerStore = Mage::app()->getStore()->getName();               
                                
                $emailTemplate->setSenderName($buyerName);
                $emailTemplate->setSenderEmail($buyerEmail); 

                /**
                 * To set cancel/refund request sent
                 */                
                if($shippingStatus == 1){
                $requestedType = $this->__('cancellation');	            
                Mage::getModel('marketplace/order')->updateSellerRequest($selectedItemproductId,$orderId,$loggedInCustomerId,$sellerId,0);               
                }else{
                $requestedType = $this->__('return');                         
                Mage::getModel('marketplace/order')->updateSellerRequest($selectedItemproductId,$orderId,$loggedInCustomerId,$sellerId,1);                               
                }          
                
                $emailTemplateVariables = array(
                		'ownername' => $sellerName,
                		'productdetails' => $productDetailsHtml,
                		'order_id' => $incrementId,
                		'customer_email' => $buyerEmail,
                		'customer_firstname' => $buyerName,
                		'reason' => $data['reason'],
                		'requesttype' => $requestedType,
                		'requestperson' => $this->__('Customer')
                );
                
                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                /**
                 *  Sending email to admin
                */
                $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailSent =  $emailTemplate->send($recipient, $sellerName, $emailTemplateVariables);
                } 
                
                if($shippingStatus == 1){
                Mage::getSingleton('core/session')->addSuccess($this->__("Item cancellation request has been sent successfully."));
                }else{
                Mage::getSingleton('core/session')->addSuccess($this->__("Item return request has been sent successfully."));
                }                     
                $this->_redirect('sales/order/view/order_id/'.$data['order_id']);
                
                } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('sales/order/view/order_id/'.$data['order_id']); 
                }           
   		}else{
   			Mage::getSingleton('core/session')->addError($this->__("You do not have permission to access this page"));   			
   			$this->_redirect('sales/order/view/order_id/'.$orderId);
   		}   	
   }
   
} 