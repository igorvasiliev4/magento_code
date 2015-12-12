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

/**
 * Function written in this file are globally accessed
 */
class Apptha_Marketplace_Helper_General extends Mage_Core_Helper_Abstract {
    /**
     * Get email template
     *
     * @param number $status            
     * @return number $templateId
     */
    public function getEmailTemplate($status) {
        if ($status == 1) {
            $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductenabledemailnotificationtemplate' );
        } else {
            $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductdisabledemailnotificationtemplate' );
        }
        
        if ($templateId) {
            $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
        } else {
            $emailTemplate = Mage::helper ( 'marketplace/market' )->getEmailTemplate ( $status );
        }
        
        return $emailTemplate;
    }
    
    /**
     * Get auto product name
     *
     * @param string $autoProductname            
     * @param array $options            
     * @param array $productData            
     * @param string $key            
     * @return string $autoProductName
     */
    public function getAutoProductName($autoProductName, $options, $productData, $key) {
        foreach ( $options as $option ) {
            if ($productData [$key] == $option ['value']) {
                $autoProductName = $autoProductName . $option ['label'] . '-';
            }
        }
        return $autoProductName;
    }
    
    /**
     * Delete products
     *
     * @param array $productCollections            
     */
    public function deleteProducts($productCollections) {
        foreach ( $productCollections as $product ) {
            $productId = $product->getEntityId ();
            $model = Mage::getModel ( 'catalog/product' )->load ( $productId );
            $model->delete ();
        }
    }
    /**
     * Get seller approval email template
     *
     * @param
     *            $templateId
     * @return $emailTemplate
     */
    public function getSellerApprovalEmailTemplate($templateId, $value) {
        if ($templateId) {
            $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
        } else {
            if ($value == 1) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_seller_email_template_selection' );
            } elseif ($value == 3) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_seller_review_approve_review' );
            } elseif ($value == 4) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_contact_email_template_selection' );
            } else {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_seller_email_template_disapprove' );
            }
        }
        return $emailTemplate;
    }
    /**
     * Get address errors
     *
     * @param array $errors            
     * @param array $addressErrors            
     * @return array $errors
     */
    public function getAddressErrors($errors, $addressErrors) {
        if (is_array ( $addressErrors )) {
            $errors = array_merge ( $errors, $addressErrors );
        }
        return $errors;
    }
    
    /**
     * Change assign product id
     *
     * @param number $entity_id            
     * @return void
     */
    public function changeAssignProductId($entity_id) {
        $getFirstAssignProduct = $assignProducts = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addFieldToFilter ( 'assign_product_id', $entity_id )->addAttributeToFilter ( 'is_assign_product', 1 )->addAttributeToFilter ( 'visibility', array (
                'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE 
        ) );
        $assignProductsCount = count ( $assignProducts );
        if ($assignProductsCount >= 1) {
            $firstAssignProduct = $getFirstAssignProduct->getFirstItem ();
            if ($firstAssignProduct->getEntityId ()) {
                $storeId = Mage::app ()->getStore ()->getStoreId ();
                Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
                $firstAssignProductData = Mage::getModel ( 'catalog/product' )->load ( $firstAssignProduct->getEntityId () );
                $firstAssignProductData->setIsAssignProduct ( 0 );
                $firstAssignProductData->setAssignProductId ( '' );
                $firstAssignProductData->setVisibility ( Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH );
                $firstAssignProductData->save ();
                $newAssignProductId = $firstAssignProductData->getEntityId ();
                Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
                Mage::helper ( 'marketplace/product' )->updateAssignProductId ( $newAssignProductId, $assignProducts );
                Mage::app ()->setCurrentStore ( $storeId );
            }
        }       
    }

}