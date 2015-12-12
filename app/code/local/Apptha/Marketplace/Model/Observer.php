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
 */

/**
 * Event Observer
 */
class Apptha_Marketplace_Model_Observer {
    
    /**
     * Order saved successfully then commisssion information will be saved in database and email notification
     * will be sent to seller
     *
     * Order information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function successAfter($observer) {
        $sellerDefaultCountry = '';
        $nationalShippingPrice = $internationalShippingPrice = 0;
        $orderIds = $observer->getEvent ()->getOrderIds ();
        $order = Mage::getModel ( 'sales/order' )->load ( $orderIds [0] );
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $getCustomerId = $customer->getId ();
        $grandTotal = $order->getGrandTotal ();
        $status = $order->getStatus ();
        $itemCount = 0;
         $shippingCountryId = '';
        $items = $order->getAllItems ();
        $orderEmailData = array ();
        foreach ( $items as $item ) {
            $getProductId = $item->getProductId ();
            $createdAt = $item->getCreatedAt ();
            $paymentMethodCode = $order->getPayment ()->getMethodInstance ()->getCode ();
            $products = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $getProductId );
            $sellerId = $products->getSellerId ();
            $productType = $products->getTypeID ();
            /**
             * Get the shipping active status of seller
             */
            $sellerShippingEnabled = Mage::getStoreConfig ( 'carriers/apptha/active' );
            if ($sellerShippingEnabled == 1 && $productType == 'simple') {
                /**
                 * Get the product national shipping price
                 * and international shipping price
                 * and shipping country
                 */
                $nationalShippingPrice = $products->getNationalShippingPrice ();
                $internationalShippingPrice = $products->getInternationalShippingPrice ();
                $sellerDefaultCountry = $products->getDefaultCountry ();
                $shippingCountryId = $order->getShippingAddress ()->getCountry ();
            }
            /**
             * Check seller id has been set
             */
            if ($sellerId) {
            	$orderPrice = $item->getPrice() * $item->getQtyOrdered();
            	$productAmt = $item->getPrice();
            	$productQty = $item->getQtyOrdered();            	
                if ($paymentMethodCode == 'paypaladaptive') {
                    $credited = 1;
                } else {
                    $credited = 0;
                }
                $shippingPrice = Mage::helper ( 'marketplace/market' )->getShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
                /**
                 * Getting seller commission percent
                 */
                $sellerCollection = Mage::helper ( 'marketplace/marketplace' )->getSellerCollection ( $sellerId );
                $percentperproduct = $sellerCollection ['commission'];
                $commissionFee = $orderPrice * ($percentperproduct / 100);
                $sellerAmount = $shippingPrice - $commissionFee;
                /**
                 * Storing commission information in database table
                 */
                if($commissionFee > 0 || $sellerAmount > 0 ){
                $commissionDataArr = array (
                        'seller_id' => $sellerId,
                        'product_id' => $getProductId,
                        'product_qty' => $productQty,
                        'product_amt' => $productAmt,
                        'commission_fee' => $commissionFee,
                        'seller_amount' => $sellerAmount,
                        'order_id' => $order->getId (),
                        'increment_id' => $order->getIncrementId (),
                        'order_total' => $grandTotal,
                        'order_status' => $status,
                        'credited' => $credited,
                        'customer_id' => $getCustomerId,
                        'status' => 1,
                        'created_at' => $createdAt,
                        'payment_method' => $paymentMethodCode 
                );
                $commissionId = $this->storeCommissionData ( $commissionDataArr );
                $orderEmailData [$itemCount] ['seller_id'] = $sellerId;
                $orderEmailData [$itemCount] ['product_qty'] = $productQty;
                $orderEmailData [$itemCount] ['product_id'] = $getProductId;
                $orderEmailData [$itemCount] ['product_amt'] = $productAmt;
                $orderEmailData [$itemCount] ['commission_fee'] = $commissionFee;
                $orderEmailData [$itemCount] ['seller_amount'] = $sellerAmount;
                $orderEmailData [$itemCount] ['increment_id'] = $order->getIncrementId ();
                $orderEmailData [$itemCount] ['customer_firstname'] = $order->getCustomerFirstname ();
                $orderEmailData [$itemCount] ['customer_email'] = $order->getCustomerEmail ();
                $itemCount = $itemCount + 1;
                }
            }
            if ($paymentMethodCode == 'paypaladaptive') {
                $this->updateCommissionPA ( $commissionId );
            }
        }
        if (Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/sales_notification' ) == 1) {
            $this->sendOrderEmail ( $orderEmailData );
        }
    }
    
    /**
     * Update commission while uisng PayPal Adaptive
     */
    public function updateCommissionPA($commissionId) {
        /**
         * If payment method is paypal adaptive, then commission table(credited to seller) and transaction table(amout paid to seller) will be updated
         */
        $model = Mage::helper ( 'marketplace/transaction' )->getCommissionInfo ( $commissionId );
        
        /**
         * Get the Commission Fee of admin
         */
        $adminCommission = $model->getCommissionFee ();
        /**
         * Get the seller amount
         */
        $sellerCommission = $model->getSellerAmount ();
        /**
         * Get the Seller Id
         */
        $sellerId = $model->getSellerId ();
        /**
         * Get commission & order id
         */
        $commissionId = $model->getId ();
        $orderId = $model->getOrderId ();
        
        /**
         * transaction collection to update the payment information
         */
        $transaction = Mage::helper ( 'marketplace/transaction' )->getTransactionInfo ( $commissionId );
        $transactionIdVal = $transaction->getId ();
        /**
         * check transaction id is empty
         * if so update the transaction data like
         * commission id
         * seller id
         * seller commission
         * admin commission
         * order id in a variable
         * and save the transaction data
         */
        if (empty ( $transactionIdVal )) {
            $Data = array (
                    'commission_id' => $commissionId,
                    'seller_id' => $sellerId,
                    'seller_commission' => $sellerCommission,
                    'admin_commission' => $adminCommission,
                    'order_id' => $orderId,
                    'received_status' => 0 
            );
            Mage::helper ( 'marketplace/transaction' )->saveTransactionData ( $Data );
        }
        /**
         * Update the database after admin paid seller amount
         */
        $transactions = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToSelect ( 'id' )->addFieldToFilter ( 'paid', 0 );
        foreach ( $transactions as $transaction ) {
            $transactionIdVal = $transaction->getId ();
            /**
             * Check the transaction id is not empty
             */
            if (! empty ( $transactionIdVal )) {
                /**
                 * Update the transaction Details
                 */
                Mage::helper ( 'marketplace/transaction' )->updateTransactionData ( $transactionIdVal );
            }
        }
    }
    
    /**
     * Save seller commission data in database and get the commission id
     *
     * Commission information passed to update in database
     *
     * @param array $commissionDataArr
     *            This function will return the commission id of the last saved data
     * @return int
     */
    public function storeCommissionData($commissionDataArr) {
        $model = Mage::getModel ( 'marketplace/commission' );
        $model->setData ( $commissionDataArr );
        $model->save ();
        return $model->getId ();
    }
    
    /**
     * Send Order Email to seller
     *
     * Passed the order information to send with email
     *
     * @param array $orderEmailData            
     *
     * @return void
     */
    public function sendOrderEmail($orderEmailData) {
        $sellerIds = array ();
        $displayProductCommission = Mage::helper ( 'marketplace' )->__ ( 'Commission Fee' );
        $displaySellerAmount = Mage::helper ( 'marketplace' )->__ ( 'Seller Amount' );
        $displayProductName = Mage::helper ( 'marketplace' )->__ ( 'Product Name' );
        $displayProductQty = Mage::helper ( 'marketplace' )->__ ( 'Product QTY' );
        $displayProductAmt = Mage::helper ( 'marketplace' )->__ ( 'Product Amount' );
        foreach ( $orderEmailData as $data ) {
            if (! in_array ( $data ['seller_id'], $sellerIds )) {
                $sellerIds [] = $data ['seller_id'];
            }
        }
        foreach ( $sellerIds as $key => $id ) {
            $totalProductAmt = $totalCommissionFee = $totalSellerAmt = 0;
            $productDetails = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea">';
            $productDetails .= '<thead><tr>';
            $productDetails .= '<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductName . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductQty . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductAmt . '</th>';
            $productDetails .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductCommission . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displaySellerAmount . '</th></tr></thead>';
            $productDetails .= '<tbody bgcolor="#F6F6F6">';
            $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
            foreach ( $orderEmailData as $data ) {
                if ($id == $data ['seller_id']) {
                $sellerId = $data ['seller_id'];
                $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
                $productId = $data ['product_id'];
                    $product = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );
                    $productGroupId = $product->getGroupId ();
                   $productName = $product->getName ();
                  $productDetails .= '<tr>';
                    $productDetails .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productName . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . round ( $data ['product_qty'] ) . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $data ['product_amt'], 2 ) . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $data ['commission_fee'], 2 ) . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $data ['seller_amount'], 2 ) . '</td>';
                    $totalProductAmt = $totalProductAmt + $data ['product_amt'];
                    $totalCommissionFee = $totalCommissionFee + $data ['commission_fee'];
                    $totalSellerAmt = $totalSellerAmt + $data ['seller_amount'];
                    $customerEmail = $data ['customer_email'];
                    $incrementId = $data ['increment_id'];
                    $customerFirstname = $data ['customer_firstname'];
                    $productDetails .= '</tr>';
                }
            }
            $productDetails .= '</tbody><tfoot>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px">Commision Fee</td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $totalCommissionFee, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px">Seller Amount</td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $totalSellerAmt, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px"><strong>Total Product Amount</strong></td><td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbol . round ( $totalProductAmt, 2 ) . '</span></strong></td></tr>';
            $productDetails .= '</tfoot></table>';
            if ($groupId == $productGroupId) {
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/sales_notification_template_selection' );
                
                $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                
                if ($templateId) {
                    $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_sales_notification_template_selection' );
                }
                $customer = Mage::helper ( 'marketplace/marketplace' )->loadCustomerData ( $sellerId );
                $sellerName = $customer->getName ();
                $sellerEmail = $customer->getEmail ();
                $recipient = $toMailId;
                $sellerStore = Mage::app ()->getStore ()->getName ();
                $recipientSeller = $sellerEmail;
                $emailTemplate->setSenderName ( $customerFirstname );
                $emailTemplate->setSenderEmail ( $customerEmail );
               $emailTemplateVariablesValue = (array (
                        'ownername' => $toName,
                        'productdetails' => $productDetails,
                        'order_id' => $incrementId,
                        'seller_store' => $sellerStore,
                        'customer_email' => $customerEmail,
                        'customer_firstname' => $customerFirstname 
                ));
                $emailTemplate->setDesignConfig ( array (
                        'area' => 'frontend' 
                ) );
                $emailTemplate->getProcessedTemplate ( $emailTemplateVariablesValue );
                /**
                 * Send email to the recipient
                 */
                $emailTemplate->send ( $recipient, $toName, $emailTemplateVariablesValue );
                $emailTemplateVariablesValue = (array (
                        'ownername' => $sellerName,
                        'productdetails' => $productDetails,
                        'order_id' => $incrementId,
                        'seller_store' => $sellerStore,
                        'customer_email' => $customerEmail,
                        'customer_firstname' => $customerFirstname 
                ));
                $emailTemplate->send ( $recipientSeller, $sellerName, $emailTemplateVariablesValue );
            }
        }
    }
    
    /**
     * Setting Cron job to enable/disable vacation mode by seller
     *
     * @return void
     */
    public function eventVacationMode() {
        $currentDate = date ( "Y-m-d ", Mage::getModel ( 'core/date' )->timestamp ( time () ) );
        $vacationInfo = Mage::getModel ( 'marketplace/vacationmode' )->getCollection ()->addFieldToSelect ( '*' );
        foreach ( $vacationInfo as $_vacationInfo ) {
            /**
             * Get Vacation info from date
             */
            $fromDate = $_vacationInfo ['date_from'];
            /**
             * Get Vacation info to date
             */
            $toDate = $_vacationInfo ['date_to'];
            /**
             * Get Seller id of each vacation
             */
            $sellerId = $_vacationInfo ['seller_id'];
            /**
             * Get product disabled status of each vacation product
             */
            $productStatus = $_vacationInfo ['product_disabled'];
            $product = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $sellerId );
            $productId = array ();
            foreach ( $product as $_product ) {
                $productId [] = $_product->getId ();
            }
            Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
            /**
             * Confirm the vacation is active by checking
             * current date is greater than or equal to vacation from-date
             * and current date is less than or equal to vacation to-date
             * and vacation product status is equal to zero
             * if so update the product status to 2
             */
            if ($currentDate >= $fromDate && $currentDate <= $toDate && $productStatus == 0) {
                foreach ( $productId as $_productId ) {
                    Mage::getModel ( 'catalog/product' )->load ( $_productId )->setStatus ( 2 )->save ();
                }
            }
            /**
             * check the current date is less than vacation from-date
             * and current date is greater than vacation to-date
             * if so update the product status to 1
             */
            if ($currentDate < $fromDate || $currentDate > $toDate) {
                foreach ( $productId as $_productId ) {
                    Mage::getModel ( 'catalog/product' )->load ( $_productId )->setStatus ( 1 )->save ();
                }
            }
        }
    }
    
    /**
     * Change status to disable for deleted seller products.
     *
     * @param object $observer            
     */
    public function customerdelete($observer) {
        $customer = $observer->getCustomer ();
        $productCollections = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $customer->getId () );
        foreach ( $productCollections as $product ) {
            $productId = $product->getEntityId ();
            Mage::helper ( 'marketplace/general' )->changeAssignProductId ( $productId );
        }
    }
    
    /**
     * Restrict seller product to buy themself
     *
     * @param object $observer            
     */
    public function addToCartEvent($observer) {
        /**
         * check the observer event gull action name is equal to the checkout cart add
         */
        if ($observer->getEvent ()->getControllerAction ()->getFullActionName () == 'checkout_cart_add') {
            /**
             * Assign the customer id as empty
             */
            $customerId = '';
            /**
             * Check the customer is currently logged in
             * if so then get the customer data
             */
            if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
                $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
                $customerId = $customerData->getId ();
            }
            
            $product = Mage::getModel ( 'catalog/product' )->load ( Mage::app ()->getRequest ()->getParam ( 'product', 0 ) );
            /**
             * Check the product id is not set
             * or cutomer id is empty
             * if so return
             */
            if (! $product->getId () || empty ( $customerId )) {
                return;
            }
            $sellerId = $product->getSellerId ();
            /**
             * check the the current customer id is equal to the seller id
             */
            if ($sellerId == $customerId) {
                
                $assignProductId = $product->getAssignProductId ();
                if (! empty ( $assignProductId )) {
                    $productUrl = Mage::getModel ( 'catalog/product' )->load ( $assignProductId )->getProductUrl ();
                } else {
                    $productUrl = $product->getProductUrl ();
                }
                
                $msg = Mage::helper ( 'marketplace' )->__ ( "Seller can't buy their own product." );
                Mage::getSingleton ( 'core/session' )->addError ( $msg );
                
                Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $productUrl );
                Mage::app ()->getResponse ()->sendResponse ();
                
                $controller = $observer->getControllerAction ();
                $controller->getRequest ()->setDispatched ( true );
                $controller->setFlag ( '', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true );
            }
        }
        return $this;
    }
    
    public function change_price(Varient_Event_Observer $observer) {
    	$item = $observer->getEvent ()->getQuoteItem ();
    	$productId = $item->getProductId ();
    	$product=Mage::getModel('catalog/product')->load($productId);
    	$price = $product->getPrice();
    	$showSplPrice = $product->getData('show_special_price');
    	if(!$showSplPrice){
	    	$item->setCustomPrice ( $price );
	    	$item->setOriginalCustomPrice ( $price );
	    	$item->getProduct ()->setIsSuperMode ( true );
    	}
    
    
    }
    
}