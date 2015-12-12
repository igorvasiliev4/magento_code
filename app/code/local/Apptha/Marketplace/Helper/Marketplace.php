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
class Apptha_Marketplace_Helper_Marketplace extends Mage_Core_Helper_Abstract {
    
    /**
     * Function to get the seller rewrite url
     *
     * Passed the seller id to rewrite the particular seller url
     *
     * @param int $sellerId
     *            This function will return the rewrited url for a particular seller
     * @return string
     */
    
   
    
    public function getSellerRewriteUrl($sellerId) {
    	/** getting seller id */
        $targetPath = 'marketplace/seller/displayseller/id/' . $sellerId;
        $mainUrlRewrite = Mage::getModel ( 'core/url_rewrite' )->load ( $targetPath, 'target_path' );
        $getRequestPath = $mainUrlRewrite->getRequestPath ();
        return Mage::getUrl ( $getRequestPath );
    }
    
    /**
     * Function to load particular seller information
     *
     * In this function seller id is passed to get particular seller data
     *
     * @param int $_id
     *            This function will return the particular seller information as array
     * @return array
     */
    public function getSellerCollection($_id) {
    	/** load collection based on seller id */
        return Mage::getModel ( 'marketplace/sellerprofile' )->load ( $_id, 'seller_id' );
    }
    
    /**
     * Function to load particular category information
     *
     * Passed Category Id to get the category information
     *
     * @param int $catId
     *            This function will return the Category information as array
     * @return array
     */
    public function getCategoryData($catId) {
    	/** load category based on category id */
        return Mage::getModel ( 'catalog/category' )->load ( $catId );
    }
    
    /**
     * Function to delete product
     *
     * Product entity id are passed to delete the product
     *
     * @param int $entityIds
     *            This function will return true or false
     * @return bool
     */
    public function deleteProduct($entityIds) {
        $productSellerId = Mage::getModel ( 'catalog/product' )->load ( $entityIds )->getSellerId ();
        if ($productSellerId == Mage::getSingleton ( 'customer/session' )->getCustomerId ()) {
            Mage::helper ( 'marketplace/general' )->changeAssignProductId ( $entityIds );
            Mage::getModel ( 'catalog/product' )->setId ( $entityIds )->delete ();
        }
        return true;
    }
    
    /**
     * Function to get product collection
     *
     * Product id is passed to get the particular product information
     *
     * @param int $getProductId
     *            This function will display the particular product information as array
     * @return array
     */
    public function getProductInfo($getProductId) {
        return Mage::getModel ( 'catalog/product' )->load ( $getProductId );
    }
    
    /**
     * Function to load email template
     *
     * Passed the template id to load the email template
     *
     * @param int $templateId
     *            This function will return the email template
     * @return string
     */
    public function loadEmailTemplate($templateId) {
        return Mage::getModel ( 'core/email_template' )->load ( $templateId );
    }
    
    /**
     * Function to load customer data
     *
     * Passed the selle id to load a particular seller details
     *
     * @param int $sellerId
     *            This function will return the seller details as array
     * @return array
     */
    public function loadCustomerData($sellerId) {
    	/** To load customer based on seller id */
        return Mage::getModel ( 'customer/customer' )->load ( $sellerId );
    }
    
    /**
     * Function to storing downloadable product link data
     *
     * Downloadable file data are passed as array
     *
     * @param array $linkModel
     *            This function will return true or false
     * @return bool
     */
    public function saveDownLoadLink($linkModel) {
    	/** to save download link */
        $linkModel->save ();
        return true;
    }
    
    /**
     * Function to set product instock
     *
     * Passed the Product is instock or not value
     *
     * @param int $isInStock
     *            This function will return 0 or 1
     * @return bool
     */
    public function productInStock($isInStock) {
        if (isset ( $isInStock )) {
            return $stock_data ['is_in_stock'] = $isInStock;
        } else {
            return $stock_data ['is_in_stock'] = 1;
        }
    }
    
    /**
     * Function to get vacation mode savae url
     *
     * This Function will return the redirect url of vacation mode save action
     *
     * @return string
     */
    public function getVacationModeSaveUrl() {
    	/** To check vacation mode */
        return Mage::getUrl ( 'marketplace/general/vacationmodesave' );
    }
        
    /**
     * Function to delete deal price and date for products
     *
     * Passed the entity id in url to get the product details
     *
     * @param int $entityIds
     *            This Function will delete deal details
     * @return bool
     */
    public function deleteDeal($entityIds) {
        Mage::getModel ( 'catalog/product' )->load ( $entityIds )->setSpecialFromDate ( '' )->setSpecialToDate ( '' )->setSpecialPrice ( '' )->save ();
        return true;
    }
    
    /**
     * Retrieve attribute id for seller shipping
     *
     * This function will return the seller shipping id
     *
     * @return int
     */
    public function getSellerShipping() {
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'seller_shipping_option' );
    }
    /**
     * Load particular product info
     *
     * @param Mage_Catalog_Model_Product $product            
     */
    protected function _loadProduct(Mage_Catalog_Model_Product $product) {
        $product->load ( $product->getId () );
    }
    /**
     * Get the New and Sale Label for a particular product
     *
     * @param Mage_Catalog_Model_Product $product            
     * @return string
     */
    public function getLabel(Mage_Catalog_Model_Product $product) {
        $html = '';
        $this->_loadProduct ( $product );
        if ($this->_isNew ( $product )) {
            $html .= '<div class="new-label new-right' . '">New</div>';
        }
        if ($this->_isOnSale ( $product )) {
            $html .= '<div class="sale-label sale-left">Sale</div>';
        }
        return $html;
    }
    /**
     * Checking the from and to date for new and sale product
     *
     * @param unknown $from            
     * @param unknown $to            
     * @return boolean
     */
    protected function _checkDate($from, $to) {
        $return = true;
        $date = date( 'Y-m-d');
        $today = strtotime ($date);
        if ($from && $today < $from) {
            $return = false;
        }
        if ($to && $today > $to) {
            $return = false;
        }
        if (! $to && ! $from) {
            $return = false;
        }
        return $return;
    }
    /**
     * Check whether a product is set as new
     *
     * @param unknown $product            
     */
    protected function _isNew($product) {
        $from = strtotime ( $product->getData ( 'news_from_date' ) );
        $to = strtotime ( $product->getData ( 'news_to_date' ) );
        return $this->_checkDate ( $from, $to );
    }
    /**
     * check whether a product is set for sale
     *
     * @param unknown $product            
     */
    protected function _isOnSale($product) {
        $from = strtotime ( $product->getData ( 'special_from_date' ) );
        $to = strtotime ( $product->getData ( 'special_to_date' ) );
        return $this->_checkDate ( $from, $to );
    }
    /**
     * Get category display url
     *
     * Return the category display url
     *
     * @return string
     */
    public function getCategoryDisplayUrl($category) {
        $subCatId = array ();
        $children = Mage::getModel ( 'catalog/category' )->getCategories ( $category );
        foreach ( $children as $_children ) {
            $subCatId [] = $_children->getId ();
        }
        if (count ( $subCatId ) > 0) {
            return Mage::getUrl ( 'marketplace/index/categorydisplay', array (
                    'id' => $category 
            ) );
        } else {
            $catInfo = Mage::getModel ( 'catalog/category' )->load ( $category );
            return Mage::getBaseUrl () . $catInfo->getUrlPath ();
        }
    
    }
    /**
     * Resize category images to display
     *
     * Return image url
     *
     * @return string
     */
    public function getResizedImage($imagePath, $width, $height = null, $quality = 100) {
        
        $return = '';
        $imageUrl = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . "category" . DS . $imagePath;
        
        if (! $imagePath || ! is_file ( $imageUrl )) {
            $return = false;
        } else {
            /**
             * Because clean Image cache function works in this folder only
             */
            $imageResized = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'product' . DS . "cache" . DS . "cat_resized" . DS . $width . $imagePath;
            if (! file_exists ( $imageResized ) && file_exists ( $imageUrl ) || file_exists ( $imageUrl ) && filemtime ( $imageUrl ) > filemtime ( $imageResized )) :
                $imageObj = new Varien_Image ( $imageUrl );
                $imageObj->constrainOnly ( true );
                $imageObj->keepAspectRatio ( false );
                $imageObj->keepFrame ( false );
                $imageObj->quality ( $quality );
                $imageObj->resize ( $width, $height );
                $imageObj->save ( $imageResized );
            
   
endif;
            
            if (file_exists ( $imageResized )) {
                $return = Mage::getBaseUrl ( 'media' ) . "catalog/product/cache/cat_resized/" . $width . $imagePath;
            } else {
                $return = $imagePath;
            }
        }
        return $return;
    
    }
    
    /**
     * Function to get the dashboard url
     *
     * This Function will return the redirect url to dashboard
     *
     * @return string
     */
    public function dashboardUrl() {
    	return Mage::getUrl ( 'marketplace/seller/dashboard' );
    }

}