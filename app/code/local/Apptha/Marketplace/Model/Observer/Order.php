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
class Apptha_Marketplace_Model_Observer_Order {
    
    /**
     * If Order status changed successfully then commisssion information will be saved in database and email notification
     * will be sent to seller
     *
     * @return void
     */
    public function salesOrderAfter() {
        /**
         * Define shipping country id
         * and seller default country as empty
         */
        $shippingCountryId = $sellerDefaultCountry = '';
        /**
         * /**
         * Define national shipping price
         * and internation shipping price as zero
         */
        $nationalShippingPrice = $internationalShippingPrice = 0;
        $orderId = ( int ) Mage::app ()->getRequest ()->getParam ( 'order_id' );   
        if ($orderId) {
            $orders = Mage::getModel ( 'sales/order' )->load ( $orderId );
            $statusOrder = $orders->getStatus ();
            $commissions = Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToSelect (array('id','order_status'));
            $count = count ( $commissions );
            /**
             * Check the commission count is greater than zero
             * if it is then get the commission id
             */
            if ($count > 0) {
                foreach ( $commissions as $commission ) {
                    $commissionId = $commission->getId ();
                    if (!empty($commissionId) && $commission->getOrderStatus() != 'canceled') {
                    Mage::helper ( 'marketplace/transaction' )->updateCommissionData ( $statusOrder, $commissionId );
                    }
                }
            } else {       
                $order = Mage::getModel ( 'sales/order' )->load ( $orderId );
                $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
                $getCustomerId = $customer->getId ();
                $grandTotal = $order->getGrandTotal ();
                $status = $order->getStatus ();
                $items = $order->getAllItems ();
                foreach ( $items as $item ) {                	
                    /**
                     * Get the product Id
                     */
                    $getProductIdValue = $item->getProductId ();
                    $createdAt = $item->getCreatedAt ();
                    /**
                     * Get Payment Method
                     */
                    $paymentMethodCodeValue = $order->getPayment ()->getMethodInstance ()->getCode ();
                    $products = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $getProductIdValue );
                    $productType = $products->getTypeID ();
                    /**
                     * Get seller shipping active status from admin configuration
                     */
                    $sellerShippingEnabled = Mage::getStoreConfig ( 'carriers/apptha/active' );
                
                    $getShippingDetails = Mage::helper ( 'marketplace/market' )->getShippingDetails ( $sellerShippingEnabled, $productType, $products, $orders );
                    $nationalShippingPrice = $getShippingDetails ['national_shipping_price'];
                    $internationalShippingPrice = $getShippingDetails ['international_shipping_price'];
                    $sellerDefaultCountry = $getShippingDetails ['seller_default_country'];
                    $shippingCountryId = $getShippingDetails ['shipping_country_id'];
                    $sellerId = $products->getSellerId ();
                    $credited = 1;
                    $orderPrice = $item->getPrice () * $item->getQtyOrdered ();
                    $productAmt = $item->getPrice ();
                    $productQty = $item->getQtyOrdered ();
                    $shippingPrice = Mage::helper ( 'marketplace/market' )->calculateShippingPrice ( $sellerId, $sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
                    $sellerCollection = Mage::helper ( 'marketplace/marketplace' )->getSellerCollection ( $sellerId );
                    $percentPerProduct = $sellerCollection ['commission'];
                    /**
                     * Calculate the commision Fee for admin
                     */
                    $commissionFee = $orderPrice * ($percentPerProduct / 100);
                    /**
                     * Calculate the seller amount
                     */
                    $sellerAmount = $shippingPrice - $commissionFee;
                    /**
                     * Store commission Data information
                     */
                    $commissionData = array ('seller_id' => $sellerId,'product_id' => $getProductIdValue,'product_qty' => $productQty,                      'product_amt' => $productAmt,
                    'commission_fee' => $commissionFee,'seller_amount' => $sellerAmount,'order_id' => $order->getId (),'increment_id' => $order->getIncrementId (),'order_total' => $grandTotal,'order_status' => $status,'credited' => $credited,'customer_id' => $getCustomerId,'status' => 1,'created_at' => $createdAt,'payment_method' => $paymentMethodCodeValue 
                    );
                    $commissionId = $this->storeCommissionData ( $commissionData );
                }
                $this->updateCommissionDetailsForPA ( $paymentMethodCodeValue, $commissionId );
            }
        }
    }
    
    /**
     * Update commission details Using PayPal Adaptive
     *
     * @param string $paymentMethodCodeValue            
     * @param integer $commissionId            
     */
    public function updateCommissionDetailsForPA($paymentMethodCodeValue, $commissionId) {
        /**
         * Check Payment method code is paypaladaptive
         */
        if ($paymentMethodCodeValue == 'paypaladaptive') {
            $model = Mage::helper ( 'marketplace/transaction' )->getCommissionInfo ( $commissionId );
            /**
             * Get Seller Id
             */
            $sellerId = $model->getSellerId ();
            /**
             * Get Admin commission Fee
             */
            $adminCommission = $model->getCommissionFee ();
            /**
             * Get Seller Amount
             */
            $sellerCommission = $model->getSellerAmount ();
            /**
             * Get the order information like
             * order id
             * commission id
             * transaction details
             */
            $orderId = $model->getOrderId ();
            $commissionId = $model->getId ();
            $transaction = Mage::helper ( 'marketplace/transaction' )->getTransactionInfo ( $commissionId );
            $transactionId = $transaction->getId ();
            /**
             * Confirm the transaction id is not empty
             * if so assign the values in array like
             * commissionid
             * seller id
             * seller commission
             * admin commission
             * order id
             * received status
             */
            if (empty ( $transactionId )) {
                $Data = array (
                        'commission_id' => $commissionId,
                        'seller_id' => $sellerId,
                        'seller_commission' => $sellerCommission,
                        'admin_commission' => $adminCommission,
                        'order_id' => $orderId,
                        'received_status' => 0 
                );
                /**
                 * Save Transaction Details
                 */
                Mage::getModel ( 'marketplace/transaction' )->setData ( $Data )->save ();
            }
            $transactions = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToSelect ( 'id' )->addFieldToFilter ( 'paid', 0 );
            foreach ( $transactions as $transaction ) {
                /**
                 * Get the id of transaction
                 */
                $transactionId = $transaction->getId ();
                /**
                 * Check the transaction id is not equal to empty
                 * if so then update the transaction details
                 */
                if (! empty ( $transactionId )) {
                    Mage::helper ( 'marketplace/transaction' )->updateTransactionData ( $transactionId );
                }
            }
        }
    }
    
    /**
     * creditmemo(Refund process)
     *
     * Order information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function creditMemoEvent(Varien_Event_Observer $observer) {
        
        $orderId = ( int ) Mage::app ()->getRequest ()->getParam ( 'order_id' );
        $creditmemo = $observer->getEvent ()->getCreditmemo ();
        $items = $creditmemo->getAllItems ();
        foreach ( $items as $item ) {        	
        	$itemsArr = array();
        	$itemsArr[] = $item;        
        	Mage::getModel('marketplace/order')->updateOrderStatusForSellerItems($itemsArr,$orderId);
        	
            $getProductIdValue = $item->getProductId ();
            /**
             * Gettings commission information in database table
             */
            $commissions = Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'product_id', $getProductIdValue )->addFieldToSelect ( 'id' )->addFieldToSelect ( 'product_qty' );
            foreach ( $commissions as $commission ) {
                $commissionId = $commission->getId ();
                $commissionQty = $commission->getProductQty ();
                $qty = $commissionQty - $item->getQty ();
                $sellerId = $commission->getSellerId ();
                $orderPrice = $item->getPrice () * $qty;
                /**
                 * Gettings seller information in database table
                 */
                $sellerCollection = Mage::helper ( 'marketplace/marketplace' )->getSellerCollection ( $sellerId );
                $percentperproduct = $sellerCollection ['commission'];
                /**
                 * Calculate admin commission Fee
                 */
                $commissionFee = $orderPrice * ($percentperproduct / 100);
                $sellerAmount = $orderPrice - $commissionFee;
                /**
                 * Check whether seller amount is empty
                 * if it is assign status is
                 * else status is 1
                 */
                if (empty ( $sellerAmount )) {
                    $status = 0;
                } else {
                    $status = 1;
                }
                /**
                 * update commission information in database table
                 */
                if (! empty ( $commissionId )) {
                    $Data = array (
                            'product_qty' => $qty,
                            'commission_fee' => $commissionFee,
                            'seller_amount' => $sellerAmount,
                            'status' => $status 
                    );
                    /**
                     * Save Commission Data
                     */
                    Mage::helper ( 'marketplace/transaction' )->saveCommissionData ( $Data, $commissionId );
                }
            }
        }
    }
    
    /**
     * Email notification will be sent to seller after admin cancel a order
     *
     * @return void
     */
    public function cancelOrderEmail($observer) {
        $orderIds = $observer->getEvent ()->getOrder ()->getId ();
        $order = Mage::getModel ( 'sales/order' )->load ( $orderIds );
        /**
         * get Product inforation to send that details in email
         */
        $itemCount = 0;
        $items = $order->getAllItems ();
        $orderEmailData = array ();
        foreach ( $items as $item ) {       	        	
            $getProductIdValue = $item->getProductId ();
            Mage::getModel('marketplace/order')->updateCancelOrderStatusForSellerItems($getProductIdValue,$orderIds);
            $products = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $getProductIdValue );
            /**
             * Get the seller shipping active status
             */
            $sellerShippingEnabled = Mage::getStoreConfig ( 'carriers/apptha/active' );
            /**
             * Check the seller shipping status is active
             */
            if ($sellerShippingEnabled == 1) {
                /**
                 * Get the national shipping price of product
                 */
                $nationalShippingPrice = $products->getNationalShippingPrice ();
                /**
                 * Get the international shipping price of product
                 */
                $internationalShippingPrice = $products->getInternationalShippingPrice ();
                /**
                 * Get the default country of product seller
                 */
                $sellerDefaultCountry = $products->getDefaultCountry ();
                /**
                 * Get the shipping country or order
                 */
                
                $shippingCountryId = $order->getShippingAddress ()->getCountry ();
            }
            $sellerId = $products->getSellerId ();
            /**
             * Checking seller Id has set
             */
            if ($sellerId) {
                /**
                 * Calculate the order price
                 */
                $orderPrice = $item->getPrice () * $item->getQtyOrdered ();
                /**
                 * Get the Product Amount
                 */
                $productAmt = $item->getPrice ();
                /**
                 * Get the ordered product Quantity
                 */
                $productQty = $item->getQtyOrdered ();
                /**
                 * Checking seller Default country with Shipping country
                 * If both are same calcualte the shipping price using national shipping price
                 * If both are not equal calculate the shipping price using internation shipping price
                 */
                if ($sellerDefaultCountry == $shippingCountryId) {
                    $shippingPrice = $orderPrice + ($nationalShippingPrice * $productQty);
                } else {
                    $shippingPrice = $orderPrice + ($internationalShippingPrice * $productQty);
                }
                /**
                 * Get seller commission percent
                 */
                $sellerCollection = Mage::helper ( 'marketplace/marketplace' )->getSellerCollection ( $sellerId );
                $percentperproduct = $sellerCollection ['commission'];
                $commissionFee = $orderPrice * ($percentperproduct / 100);
                $sellerAmount = $shippingPrice - $commissionFee;
                
                $orderEmailData [$itemCount] ['seller_id'] = $sellerId;
                $orderEmailData [$itemCount] ['product_id'] = $getProductIdValue;
                $orderEmailData [$itemCount] ['product_qty'] = round ( $productQty );
                $orderEmailData [$itemCount] ['product_amt'] = number_format ( $productAmt, 2 );
                $orderEmailData [$itemCount] ['commission_fee'] = $commissionFee;
                $orderEmailData [$itemCount] ['seller_amount'] = $sellerAmount;
                $orderEmailData [$itemCount] ['increment_id'] = $order->getIncrementId ();
                $orderEmailData [$itemCount] ['customer_email'] = $order->getCustomerEmail ();
                $orderEmailData [$itemCount] ['customer_firstname'] = $order->getCustomerFirstname ();
                $itemCount = $itemCount + 1;
            }
        }
        if (Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/cancel_order_notification' ) == 1) {
            $this->sendCancelOrderEmail ( $orderEmailData );
        }
    }
    
    /**
     * Send Cancel Order Email to seller
     *
     * Order information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function sendCancelOrderEmail($orderEmailData) {
        $sellerIdsVal = array ();   
        $displayProductQty = Mage::helper ( 'marketplace' )->__ ( 'Product QTY' );
        $displayProductAmt = Mage::helper ( 'marketplace' )->__ ( 'Product Amount' );
        $displayProductCommission = Mage::helper ( 'marketplace' )->__ ( 'Commission Fee' );
        $displaySellerAmount = Mage::helper ( 'marketplace' )->__ ( 'Seller Amount' );
        $displayProductName = Mage::helper ( 'marketplace' )->__ ( 'Product Name' );
        foreach ( $orderEmailData as $data ) {
            /**
             * Check the seller id is not in the array of whole seller id
             * if so add the seller id in seller ids array
             */
            if (! in_array ( $data ['seller_id'], $sellerIdsVal )) {
                $sellerIdsVal [] = $data ['seller_id'];
            }
        }
        foreach ( $sellerIdsVal as $key => $id ) {         
            $totalProductAmt = $totalCommissionFee = $totalSellerAmt = 0;
            $productDetailsHtml = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea"><thead><tr>';
            $productDetailsHtml .= '<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductName . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductQty . '</th>';
            $productDetailsHtml .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductAmt . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductCommission . '</th>';
            $productDetailsHtml .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displaySellerAmount . '</th></tr></thead>';
            $productDetailsHtml .= '<tbody bgcolor="#F6F6F6">';
            $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
            foreach ( $orderEmailData as $data ) {          
                if ($id == $data ['seller_id']) {
                    $sellerId = $data ['seller_id'];
                    $productId = $data ['product_id'];
                    $product = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );
                    $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
                    $productGroupId = $product->getGroupId ();
                    $productName = $product->getName ();
                    $productDetailsHtml .= '<tr>';
                    $productDetailsHtml .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productName . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $data ['product_qty'] . '</td>';
                    $productDetailsHtml .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data ['product_amt'] . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data ['commission_fee'] . '</td>';
                    $productDetailsHtml .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $data ['seller_amount'] . '</td>';
                    $totalProductAmt = $totalProductAmt + $data ['product_amt'];
                    $totalCommissionFee = $totalCommissionFee + $data ['commission_fee'];
                    $totalSellerAmt = $totalSellerAmt + $data ['seller_amount'];
                    $incrementId = $data ['increment_id'];
                    $customerEmail = $data ['customer_email'];
                    $customerFirstname = $data ['customer_firstname'];
                    $productDetailsHtml .= '</tr>';
                }
            }
            $productDetailsHtml .= '</tbody><tfoot>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px">Commision Fee</td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . $totalCommissionFee . '</span></td> </tr>
                                 <tr> <td colspan="4" align="right" style="padding:3px 9px">Seller Amount</td> <td align="center" style="padding:3px 9px"><span>' . $currencySymbol . $totalSellerAmt . '</span></td>  </tr>
                                <tr><td colspan="4" align="right" style="padding:3px 9px"><strong>Total Product Amount</strong></td><td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbol . $totalProductAmt . '</span></strong></td> </tr>
                            </tfoot></table>';
            /**
             * Confirm the group id is equal to the product group id
             * if so then get the store configured values like
             * template id
             * admin email id
             * to mail id
             * to name
             */
            if ($groupId == $productGroupId) {
                $templateIdValue = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/cancel_notification_template_selection' );
                $adminEmailIdValue = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdValue/email" );
                $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdValue/name" );
                /**
                 * Check template id has been set
                 * if set then load that particular template
                 * if not load the default template of admin approval seller registration cancel notification template
                 */
                if ($templateIdValue) {
                    $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateIdValue );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_cancel_notification_template_selection' );
                }         
                $sellerStore = Mage::app ()->getStore ()->getName ();
                $customer = Mage::helper ( 'marketplace/marketplace' )->loadCustomerData ( $sellerId );
                $sellerEmail = $customer->getEmail ();
                $sellerName = $customer->getName ();
                $recipient = $toMailId;
                $recipientSeller = $sellerEmail;
                $emailTemplate->setSenderName ( $toName );
                $emailTemplate->setSenderEmail ( $toMailId );
                /**
                 * Dynamically replacing the email template variables with the retrieved values
                 */
                $emailTemplateVariables = (array ('ownername' => $toName,'productdetails' => $productDetailsHtml,'order_id' => $incrementId,'seller_store' => $sellerStore,'customer_email' => $customerEmail,'customer_firstname' => $customerFirstname));
                $emailTemplate->setDesignConfig ( array ('area' => 'frontend') );
                $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                /**
                 * Send email using dyanamically replaced template
                 */
                $emailTemplate->send ( $recipient, $toName, $emailTemplateVariables );
                /**
                 * Sending email to seller
                 */
                $emailTemplateVariables = (array ('ownername' => $sellerName,'productdetails' => $productDetailsHtml,'order_id' => $incrementId,'seller_store' => $sellerStore,'customer_email' => $customerEmail,'customer_firstname' => $customerFirstname));
                $emailTemplate->send ( $recipientSeller, $sellerName, $emailTemplateVariables );
            }
        }
    }
    
    /**
     * Send invoice mail to seller
     *
     * @param object $observer            
     */
    public function sendInvoiceMailToSeller($observer) {
        /**
         * Check the invoiced order notification is equal to 1
         * if so then get the information like
         * even
         * invoice details
         * order data
         */
    	$nationalShippingPrice = $internationalShippingPrice = $sellerDefaultCountry = $shippingCountryId = '';
    	$event = $observer->getEvent ();
    	$invoice = $event->getInvoice ();
    	$orderData = $invoice->getOrder ();
    	$orderIds = $orderData->getId ();
    	$order = Mage::getModel ( 'sales/order' )->load ( $orderIds );
    	/**
    	 * Get Product inforation to send that details in email
    	 */
    	$itemCount = 0;
    	/**
    	 * Get all the order items
    	 */
    	$items = $order->getAllItems ();
    	$orderEmailData = array ();
    	
    	foreach ( $items as $item ) {
    		$itemsArr = array();
    		$itemsArr[] = $item;
    		Mage::getModel('marketplace/order')->updateOrderStatusForSellerItems($itemsArr,$orderIds);
    	}
    	
        if (Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/invoiced_order_notification' ) == 1) {
            foreach ( $items as $item ) {
                $getProductIdValue = $item->getProductId ();
                $products = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $getProductIdValue );
                $productType = $products->getTypeID ();
                /**
                 * Get the shipping enables status
                 */
                $sellerShippingEnabled = Mage::getStoreConfig ( 'carriers/apptha/active' );
                /**
                 * Check the seller shipping enabled is equal to 1
                 * and product type is simple
                 * if so then get the shipping informations like
                 * national shipping price value
                 * internation shipping price value
                 * Default country of the seller
                 * shipping country id
                 */
                if ($sellerShippingEnabled == 1 && $productType == 'simple') {
                    $nationalShippingPriceVal = $products->getNationalShippingPrice ();
                    $internationalShippingPriceVal = $products->getInternationalShippingPrice ();
                    $sellerDefaultCountry = $products->getDefaultCountry ();
                    $shippingCountryId = $order->getShippingAddress ()->getCountry ();
                }
                $sellerId = $products->getSellerId ();
                /**
                 * Check the seller id has been set
                 * if set then get the information like
                 * order price value
                 * product full amount
                 * ordered product quantity
                 * shipping price
                 */
                if ($sellerId) {
                    $orderPriceVal = $item->getPrice () * $item->getQtyOrdered ();
                    $productAmt = $item->getPrice ();
                    $productQty = $item->getQtyOrdered ();
                    $shippingPrice = Mage::helper ( 'marketplace/market' )->getShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPriceVal, $nationalShippingPriceVal, $internationalShippingPriceVal, $productQty );
                    /**
                     * Get seller commission Fee
                     */
                    $sellerCollection = Mage::helper ( 'marketplace/marketplace' )->getSellerCollection ( $sellerId );
                    $percentperproduct = $sellerCollection ['commission'];
                    /**
                     * Calculate the commission fee
                     * and seller amount
                     */
                    $commissionFee = $orderPriceVal * ($percentperproduct / 100);
                    $sellerAmount = $shippingPrice - $commissionFee;
                    /**
                     * Initialise the retrieved values into the order email data
                     */
                    $orderEmailData [$itemCount] ['seller_id'] = $sellerId;
                    $orderEmailData [$itemCount] ['product_id'] = $getProductIdValue;
                    $orderEmailData [$itemCount] ['product_qty'] = $productQty;
                    $orderEmailData [$itemCount] ['product_amt'] = $productAmt;
                    $orderEmailData [$itemCount] ['commission_fee'] = $commissionFee;
                    $orderEmailData [$itemCount] ['seller_amount'] = $sellerAmount;
                    $orderEmailData [$itemCount] ['increment_id'] = $order->getIncrementId ();
                    $orderEmailData [$itemCount] ['customer_email'] = $order->getCustomerEmail ();
                    $orderEmailData [$itemCount] ['customer_firstname'] = $order->getCustomerFirstname ();
                    $itemCount = $itemCount + 1;
                }
            }
            $this->sendInvoicedOrderEmail ( $orderEmailData );
        }
    }
    
    /**
     * Send Order Invoiced Email to seller
     *
     * Order information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function sendInvoicedOrderEmail($orderEmailData) {
        $sellerIds = array ();
        /**
         * Get the invoice email information like
         * display product name
         * display seller amount
         * display product quantity
         * display product amount
         * display product commission
         */
        $displayProductName = Mage::helper ( 'marketplace' )->__ ( 'Product Name' );
        $displaySellerAmount = Mage::helper ( 'marketplace' )->__ ( 'seller_amount' );
        $displayProductQty = Mage::helper ( 'marketplace' )->__ ( 'product_qty' );
        $displayProductAmt = Mage::helper ( 'marketplace' )->__ ( 'Product Amount' );
        $displayProductCommission = Mage::helper ( 'marketplace' )->__ ( 'commission_fee' );
        foreach ( $orderEmailData as $data ) {
            if (! in_array ( $data ['seller_id'], $sellerIds )) {
                $sellerIds [] = $data ['seller_id'];
            }
        }
        foreach ( $sellerIds as $key => $id ) {
            $totalProductAmt = $totalCommissionFee = $totalSellerAmt = 0;
            $productDetails = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea"><thead><tr>';
            $productDetails .= '<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductName . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductQty . '</th>';
            $productDetails .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductAmt . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductCommission . '</th>';
            $productDetails .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displaySellerAmount . '</th></tr></thead>';
            $productDetails .= '<tbody bgcolor="#F6F6F6">';
            $currencySymbolVal = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
            foreach ( $orderEmailData as $data ) {
                if ($id == $data ['seller_id']) {
                    $sellerId = $data ['seller_id'];
                    $productId = $data ['product_id'];
                    $product = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );
                    $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
                    $productGroupId = $product->getGroupId ();
                    $productName = $product->getName ();
                    $productDetails .= '<tr><td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productName . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . round ( $data ['product_qty'] ) . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbolVal . number_format ( $data ['product_amt'], 2 ) . '</td>';
                    $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbolVal . $data ['commission_fee'] . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbolVal . $data ['seller_amount'] . '</td>';
                    $totalProductAmt = $totalProductAmt + $data ['product_amt'] * round ( $data ['product_qty'] );
                    $totalCommissionFee = $totalCommissionFee + $data ['commission_fee'];
                    $totalSellerAmt = $totalSellerAmt + $data ['seller_amount'];
                    $incrementId = $data ['increment_id'];
                    $customerEmail = $data ['customer_email'];
                    $customerFirstname = $data ['customer_firstname'];
                    $productDetails .= '</tr>';
                }
            }
            $productDetails .= '</tbody><tfoot>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px">Commision Fee</td><td align="center" style="padding:3px 9px"><span>' . $currencySymbolVal . $totalCommissionFee . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px">Seller Amount</td><td align="center" style="padding:3px 9px"><span>' . $currencySymbolVal . $totalSellerAmt . '</span></td> </tr>
                                <tr><td colspan="4" align="right" style="padding:3px 9px"><strong>Total Product Amount</strong></td> <td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbolVal . $totalProductAmt . '</span></strong></td></tr>
                            </tfoot></table>';
            if ($groupId == $productGroupId) {
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/invoiced_notification_template_selection' );
                $adminEmailIdVal = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/email" );
                $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/name" );
                if ($templateId) {
                    $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_invoiced_notification_template_selection' );
                }
                /**
                 * Loading customer data to send in email
                 */
                $customer = Mage::helper ( 'marketplace/marketplace' )->loadCustomerData ( $sellerId );
                $sellerStore = Mage::app ()->getStore ()->getName ();
                $sellerEmail = $customer->getEmail ();
                $sellerName = $customer->getName ();
                $recipient = $toMailId;
                $recipientSeller = $sellerEmail;
                $emailTemplate->setSenderEmail ( $toMailId );
                $emailTemplate->setSenderName ( $toName );
                $emailTemplateVariables = (array ('ownername' => $toName,'productdetails' => $productDetails,'order_id' => $incrementId,
                'seller_store' => $sellerStore,'customer_email' => $customerEmail,'customer_firstname' => $customerFirstname));
                $emailTemplate->setDesignConfig ( array ('area' => 'frontend') );
                $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                $emailTemplate->send ( $recipient, $toName, $emailTemplateVariables );
                /**
                 * Sending email to seller
                 * with the dynamically replaced values
                 */
                $emailTemplateVariables = (array ('ownername' => $sellerName,'productdetails' => $productDetails,'order_id' => $incrementId,
                'seller_store' => $sellerStore,'customer_email' => $customerEmail,'customer_firstname' => $customerFirstname));
                $emailTemplate->send ( $recipientSeller, $sellerName, $emailTemplateVariables );
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
}