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
class Apptha_Marketplace_Model_Observer_Product {
    
    /**
     * If product edit(enable/disable) from admin panel this event function will be called to
     * send email notification to seller
     *
     * Product information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function productEditAction($observer) {
        if (Mage::getStoreConfig ( 'marketplace/product/productmodificationnotification' )) {
            $product = array ();
            /**
             * Define productGroup Id, Seller Id, Marketplace group Id.
             * Saved Product Status as empty
             */
            $productGroupId = $sellerId = $marketplaceGroupId = $savedProductStatus = '';
            $store = 0;
            $storeName = 'All Store Views';
            $product = $observer->getProduct ();
            $productGroupId = $product->getGroupId ();
            $sellerId = $product->getSellerId ();
            $productStatus = $product->getStatus ();
            $marketplaceGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
            $observer->getStoreId ();
            /**
             * Check Store value if it is not equal to zero assign the curresponding store name else Assign the store name as 'All Store Views'
             */
            if ($store != 0) {
                $storeName = Mage::getModel ( 'core/store' )->load ( $store );
            } else {
                $storeName = 'All Store Views';
            }
            $savedProductId = $product->getId ();
            $savedProduct = Mage::getModel ( 'catalog/product' )->load ( $savedProductId );
            $savedProductStatus = $savedProduct->getStatus ();
            if ($savedProductStatus != $productStatus && count ( $savedProduct ) >= 1 && $productGroupId == $marketplaceGroupId) {
                if ($productStatus == 1) {
                    $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductenabledemailnotificationtemplate' );
                } else {
                    $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductdisabledemailnotificationtemplate' );
                }
                $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                /**
                 * Get Mail to Name
                 */
                $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                /**
                 * Selecting template id
                 */
                if ($templateId) {
                    $emailTemplateData = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {
                    $emailTemplateData = Mage::helper ( 'marketplace/market' )->getEmailTemplate ( $productStatus );
                }
                $customer = Mage::getModel ( 'customer/customer' )->load ( $sellerId );
                $selleremail = $customer->getEmail ();
                
                /**
                 * Get Name of the product
                 */
                $productname = $product->getName ();
                /**
                 * Get product url
                */
                $producturl = $product->getProductUrl ();
                /**
                 * Assign Recipient of mail
                 */
                $recipient = $selleremail;
                /**
                 * Get the name of the seller
                 */
                $sellername = $customer->getName ();
  
                $emailTemplateData->setSenderName ( $toName );
                $emailTemplateData->setSenderEmail ( $toMailId );
                /**
                 * Update Email Template with the dynamic retrieved values
                 */
                $emailTemplateVariables = (array (
                        'ownername' => $toName,
                        'sellername' => $sellername,
                        'adminemailid' => $toMailId,
                        'productname' => $productname,
                        'producturl' => $producturl,
                        'storename' => $storeName 
                ));
                $emailTemplateData->setDesignConfig ( array (
                        'area' => 'frontend' 
                ) );
                $emailTemplateData->getProcessedTemplate ( $emailTemplateVariables );
                /**
                 * Mail Sending function
                 */
                $emailTemplateData->send ( $recipient, $toName, $emailTemplateVariables );
            }
        }
    }
    
    /**
     * If multiple product are selected to edit(enable/disable) from admin panel this event function will be called to
     * send email notification to seller
     *
     * Product information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function productMassEditAction($observer) {
        /**
         * Checking whether email notification enabled or not
         */
        if (Mage::getStoreConfig ( 'marketplace/product/productmodificationnotification' )) {
            $product = $productIds = $attributesData = array ();
            $storeName = 'All Store Views';
            $storeName = 0;
            $attributesData = $observer->getAttributesData ();
            $status = $attributesData ['status'];
            $productIds = $observer->getProductIds ();
            $store = $observer->getStoreId ();
            /**
             * Check store is not equal to zero
             * and get the store name
             */
            if ($store != 0) {
                $storeName = Mage::getModel ( 'core/store' )->load ( $store );
            } /**
             * If store is equal to zero
             * Assign Store name as 'All Store Views'
             */
            else {
                $storeName = 'All Store Views';
            }
            foreach ( $productIds as $productId ) {
                /**
                 * Define Mareketplace group id, product group id, prodcut status as empty
                 */
                $marketplaceGroupId = $prdouctGroupId = $sellerId = $productStatus = '';
                /**
                 * Get Group Id
                 */
                $marketplaceGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
                $product = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );
                $prdouctGroupId = $product->getGroupId ();
                $sellerId = $product->getSellerId ();
                /**
                 * Get Product Status
                 */
                $productStatus = $product->getStatus ();
                
                if ($productStatus != $status && $prdouctGroupId == $marketplaceGroupId) {
                    
                    /**
                     * Selecting template id
                     */
                    $emailTemplate = Mage::helper ( 'marketplace/general' )->getEmailTemplate ( $status );
                    
                    $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                    $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                    $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                    
                    $customer = Mage::helper ( 'marketplace/marketplace' )->loadCustomerData ( $sellerId );
                    /**
                     * Get the seller Email
                     * and assign it as recipient
                     */
                    $selleremail = $customer->getEmail ();
                    $recipient = $selleremail;
                    /**
                     * get Seller Name
                     */
                    $sellername = $customer->getName ();
                    /**
                     * Get Product Name
                     */
                    $productname = $product->getName ();
                    /**
                     * Get Product Link
                     */
                    $producturl = $product->getProductUrl ();
                    /**
                     * Configure mail parameters
                     */
                    $emailTemplate->setSenderName ( $toName );
                    $emailTemplate->setSenderEmail ( $toMailId );
                    $emailTemplateVariables = (array (
                            'ownername' => $toName,
                            'sellername' => $sellername,
                            'adminemailid' => $toMailId,
                            'productname' => $productname,
                            'producturl' => $producturl,
                            'storename' => $storeName 
                    ));
                    $emailTemplate->setDesignConfig ( array (
                            'area' => 'frontend' 
                    ) );
                    $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                    /**
                     * sending mail
                     */
                    $emailTemplate->send ( $recipient, $toName, $emailTemplateVariables );
                }
            }
        }
    }
    
    /**
     * Admin product delete event
     */
    public function adminProductDelete($observer) {
        $product = $observer->getProduct ();
        $productId = $product->getId ();
        Mage::helper ( 'marketplace/general' )->changeAssignProductId ( $productId );
    }

}