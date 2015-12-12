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
 * This file is used to add/edit seller products
 */
class Apptha_Marketplace_ProductController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton ( 'customer/session' );
    }
    
    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        if (! $this->_getSession ()->isLoggedIn ()) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * Add New Products Form
     *
     * @return void
     */
    public function newAction() {
        /**
         * Check license key
         */
        Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
        /**
         * Initilize customer and seller group id
         */
        
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();
        
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * Save New Products
     *
     * @return void
     */
    public function newpostAction() {
        Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
        $this->checkWhetherSellerOrNot ();
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        $set = $setbase = $type = $store = $sellerId = $isInStock = '';
        /**
         * Getting product type, set, setbase, store, group id and product
         */
        $type = $this->getRequest ()->getPost ( 'type' );
        $set = $this->getRequest ()->getPost ( 'set' );
        $setbase = $this->getRequest ()->getPost ( 'setbase' );
        $store = $this->getRequest ()->getPost ( 'store' );
        $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
        $productData = $this->getRequest ()->getPost ( 'product' );
        $skuProductId = '';
        $skuProductId = Mage::getModel ( 'marketplace/product' )->checkWhetherSkuExistOrNot ( $productData );
        if ($skuProductId == 0) {
            /**
             * Getting product categories from category_ids array
             */
            $categoryIds = $this->getRequest ()->getPost ( 'category_ids' );
            
            $checkRequiredForProductSave = Mage::helper ( 'marketplace/market' )->checkRequiredForProductSave ( $productData );
            
            if ($checkRequiredForProductSave == 1 && isset ( $productData ['price'] ) && isset ( $productData ['stock_data'] ['qty'] ) && ! empty ( $type )) {
                
                /**
                 * Getting instance for catalog product collection
                 */
                $product = Mage::getModel ( 'catalog/product' );
                $imagesPath = array ();
                $uploadsData = new Zend_File_Transfer_Adapter_Http ();
                $filesDataArray = $uploadsData->getFileInfo ();
                $imagesPath = Mage::getModel ( 'marketplace/product' )->getProductImagePath ( $filesDataArray );
                $product = Mage::getModel ( 'marketplace/product' )->setProductInfo ( $product, $set, $type, $categoryIds, $sellerId, $groupId, $imagesPath );
                
                $productData = Mage::getModel ( 'marketplace/product' )->getProductDataArray ( $productData, $type );
                /**
                 * Assign configurable product data
                 */
                $attributeIds = $this->getRequest ()->getPost ( 'attributes' );
                Mage::getModel ( 'marketplace/product' )->assignConfigurableProductData ( $attributeIds, $type, $product );
                $product->addData ( $productData );
                /**
                 * Initialize dispatch event for product prepare
                 */
                Mage::dispatchEvent ( 'catalog_product_prepare_save', array (
                        'product' => $product,
                        'request' => $this->getRequest () 
                ) );
                /**
                 * Saving new product
                 */
                try {
                    $product->save ();
                    $productId = $product->getId ();
                    Mage::getModel ( 'marketplace/product' )->setConfigurableProductStockData ( $type, $product, $productData, $isInStock );
                    Mage::getModel ( 'marketplace/product' )->setBaseImageForProduct ( $productId, $store, $setbase, $productData, 'new' );
                    Mage::getModel ( 'marketplace/product' )->deleteTempImageFiles ( $imagesPath );
                    /**
                     * Function for adding downloadable product sample and link data
                     */
                    $downloadProductId = $product->getId ();
                    $this->assignDataForDownloadableProduct ( $type, $downloadProductId, $store );
                    $msg = Mage::getModel ( 'marketplace/product' )->getMessageForNewProductAdd ();
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $msg );
                    Mage::helper ( 'marketplace/product' )->sentEmailToAdmin ( $sellerId, $product );
                    Mage::app ()->setCurrentStore ( Mage::app ()->getStore ()->getStoreId () );
                    $this->redirectToConfigurablePage ( $type, $productId, $set );
                } catch ( Mage_Core_Exception $e ) {
                    /**
                     * Error message redirect to create new product page
                     */
                    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                    $this->_redirect ( 'marketplace/sellerproduct/create/' );
                } catch ( Exception $e ) {
                    /**
                     * Error message redirect to create new product page
                     */
                    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                    $this->_redirect ( 'marketplace/sellerproduct/create/' );
                }
            } else {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please enter all required fields' ) );
                if ($type == 'configurable') {
                    $this->_redirect ( 'marketplace/sellerproduct/selectattributes/', array (
                            'set' => $set 
                    ) );
                }
                $this->_redirect ( 'marketplace/sellerproduct/create' );
            }
        } else {
            /**
             * Error message redirect to create new product page
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'SKU Not Available' ) );
            $this->_redirect ( 'marketplace/sellerproduct/create/' );
        }
    }
    
    /**
     * Manage Seller Products
     *
     * @return void
     */
    public function manageAction() {
        /**
         * Check license key
         */
        Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
        
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();
        
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * Edit Existing Products
     *
     * @return void
     */
    public function editAction() {
        /**
         * Check license key
         */
        Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
        
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();
        
        /**
         * Initilize product id , customer id and seller id
         */
        $entityId = ( int ) $this->getRequest ()->getParam ( 'id' );
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $collection = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        $sellerId = $collection->getSellerId ();
        /**
         * Checking for edit permission
         */
        if ($customerId != $sellerId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You don't have enough permission to edit this product details." ) );
            $this->_redirect ( 'marketplace/product/manage' );
            return;
        }
        $customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
        /**
         * Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Admin Approval is required. Please wait until admin confirm your Seller Account' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * Save Edited Products
     *
     * @return void
     */
    public function editpostAction() {
        /**
         * Check license key
         */
        $isInStock = '';
        Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();
        $productId = $name = $description = $shortDescription = $price = $store = $sku = '';
        $categoryIds = $deleteimages = array ();
        $type = $this->getRequest ()->getPost ( 'type' );
        $productData = $this->getRequest ()->getPost ( 'product' );
        $productId = $this->getRequest ()->getPost ( 'product_id' );
        $categoryIds = $this->getRequest ()->getPost ( 'category_ids' );
        $store = Mage::app ()->getStore ()->getId ();
        $name = $productData ['name'];
        $sku = $productData ['sku'];
        $description = $productData ['description'];
        $shortDescription = $productData ['short_description'];
        $price = $productData ['price'];
        $deleteimages = $this->getRequest ()->getPost ( 'deleteimages' );
        $baseimage = $this->getRequest ()->getPost ( 'baseimage' );
        
        $checkingForProductRequiredFields = Mage::helper ( 'marketplace/market' )->checkingForProductRequiredFields ( $sku, $productId, $name, $description );
        if ($checkingForProductRequiredFields == 1 && ! empty ( $shortDescription ) && isset ( $price ) && ! empty ( $type )) {
            $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
            if (empty ( $productData ['weight'] )) {
                $productData ['weight'] = 0;
            }
            foreach($productData as $data_key => $data_value) {
            	if (array_key_exists($data_key,$product->getData())) {
            		if($productData[$data_key] != $product->getData($data_key)) {
            			$specificationFunction = 'set'.str_replace('_',"",uc_words($data_key));
            			$product->$specificationFunction($productData[$data_key]);
            		}
            
            	}
            }
            $product = Mage::getModel ( 'marketplace/product' )->setProductDataForUpdate ( $product, $categoryIds, $productData, $type, $isInStock );
            $imagesPath = array ();
            $uploadsData = new Zend_File_Transfer_Adapter_Http ();
            $filesDataArray = $uploadsData->getFileInfo ();
            $imagesPath = Mage::getModel ( 'marketplace/product' )->getProductImagePath ( $filesDataArray );
            /**
             * Adding Product images
             */
            $product = Mage::getModel ( 'marketplace/product' )->setImagesForProduct ( $product, $imagesPath );
            try {
                $product->save ();
                
                /**
                 * Removing product images
                 */
                Mage::getModel ( 'marketplace/product' )->deleteProductImagesForEdit ( $deleteimages, $productId, $baseimage );
                
                /**
                 * Set product images
                 */
                Mage::getModel ( 'marketplace/product' )->setProductImagesforProduct ( $baseimage, $productId, $store, $product, $productData );
                
                /**
                 * Checking whether image or not
                 */
                Mage::getModel ( 'marketplace/product' )->deleteTempImageFiles ( $imagesPath );
                /**
                 * Function for adding downloadable product sample and link data
                 */
                $downloadProductId = $product->getId ();
                $this->assignDataForDownloadableProduct ( $type, $downloadProductId, $store );
                Mage::app ()->setCurrentStore ( $store );
                /**
                 * Success message redirect to manage product page
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your product details are updated successfully.' ) );
                $this->_redirect ( 'marketplace/product/manage/' );
            } catch ( Mage_Core_Exception $e ) {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'marketplace/product/edit/id/' . $productId );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'marketplace/product/edit/id/' . $productId );
            }
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please enter all required fields' ) );
            $this->_redirect ( 'marketplace/product/edit/id/' . $productId );
        }
    }
    
    /**
     * Delete Seller Products
     *
     * @return void
     */
    public function deleteAction() {
        /**
         * check license key
         */
        $entity_id = '';
        Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
        
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();
        
        $entity_id = ( int ) $this->getRequest ()->getParam ( 'id' );
        $productSellerId = Mage::getModel ( 'catalog/product' )->load ( $entity_id )->getSellerId ();
        
        if (Mage::getSingleton ( 'customer/session' )->getCustomerId () == $productSellerId && Mage::getSingleton ( "customer/session" )->isLoggedIn ()) {
            /**
             * Checking whether customer approved or not
             */
            $this->loadLayout ();
            $this->renderLayout ();
            
            Mage::register ( 'isSecureArea', true );
            Mage::helper ( 'marketplace/general' )->changeAssignProductId ( $entity_id );
            Mage::getModel ( 'catalog/product' )->setId ( $entity_id )->delete ();
            
            /**
             * un set secure admin area
             */
            Mage::unregister ( 'isSecureArea' );
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Product Deleted Successfully" ) );
            $pId = $this->getRequest ()->getParam ( 'pid' );
            $set = $this->getRequest ()->getParam ( 'set' );
            if (! empty ( $pId ) && ! empty ( $set )) {
                $this->_redirect ( 'marketplace/sellerproduct/configurable/', array (
                        'id' => $pId,
                        'set' => $set 
                ) );
                return;
            }
            $isAssign = $this->getRequest ()->getParam ( 'is_assign' );
            if (! empty ( $isAssign )) {
                $this->_redirect ( 'marketplace/sellerproduct/manageassignproduct/' );
                return;
            }
            $this->_redirect ( '*/product/manage/' );
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You don't have enough permission to delete this product details." ) );
            $this->_redirect ( 'marketplace/seller/login' );
        }
    }
    
    /**
     * Manage Deals products by seller
     *
     * @return void
     */
    public function managedealsAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * Manage Deals products by seller
     *
     * @return void
     */
    public function deletesingledealAction() {
        $entityId = $this->getRequest ()->getParam ( 'id' );
        Mage::getModel ( 'catalog/product' )->load ( $entityId )->setSpecialFromDate ( '' )->setSpecialToDate ( '' )->setSpecialPrice ( '' )->save ();
        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Product Deal Deleted Successfully" ) );
        $this->_redirect ( '*/product/managedeals/' );
        return true;
    }
    
    /**
     * Function to check availability of sku
     *
     * @return int
     */
    public function checkskuAction() {
        $inputSku = trim ( $this->getRequest ()->getParam ( 'sku' ) );
        $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'sku', $inputSku );
        $count = count ( $collection );
        echo $count;
        return true;
    }
    
    /**
     * Function to display the view all compare price products
     *
     * @return void
     */
    public function comparesellerpriceAction() {
        $this->loadLayout ();
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'All Sellers' ) );
        $this->renderLayout ();
    }
    
    /**
     * Check whether seller or not
     */
    public function checkWhetherSellerOrNot() {
        /**
         * Initilize customer and seller group id
         */
        $customerGroupId = $sellerGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
        $sellerGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        $customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
        if (! $this->_getSession ()->isLoggedIn () && $customerGroupId != $sellerGroupId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        /**
         * Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
    }
    
    /**
     * Assign data to downloadable product
     *
     * @param string $type            
     * @param number $downloadProductId            
     * @param number $store            
     */
    public function assignDataForDownloadableProduct($type, $downloadProductId, $store) {
        if ($type == 'downloadable' && isset ( $downloadProductId ) && isset ( $store )) {
            $this->addDownloadableProductData ( $downloadProductId, $store );
        }
    }
    /**
     * Redirect to configurable page
     *
     * @param string $type            
     * @return boolean
     */
    public function redirectToConfigurablePage($type, $productId, $set) {
        if ($type == 'configurable') {
            $this->_redirect ( 'marketplace/sellerproduct/configurable/', array (
                    'id' => $productId,
                    'set' => $set 
            ) );
            return;
        } else {
            $this->_redirect ( 'marketplace/product/manage/' );
            return;
        }
    }
    
    /**
     * Save Downloadable Products
     *
     * Passed the downloadable product id to save files
     *
     * @param int $downloadProductId
     *            Passed the store id to save files
     * @param int $store            
     *
     * @return void
     */
    public function addDownloadableProductData($downloadProductId, $store) {
        /**
         * Initilize downloadable product sample and link files
         */
        $sampleTpath = $linkTpath = $slinkTpath = array ();
        $uploadsData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsData->getFileInfo ();
        foreach ( $filesDataArray as $key => $result ) {
            $downloadData = Mage::getModel ( 'marketplace/download' )->prepareDownloadProductData ( $filesDataArray, $key, $result );
            if (! empty ( $downloadData ['sample_tpath'] )) {
                $sampleNo = substr ( $key, 7 );
                $sampleTpath [$sampleNo] = $downloadData ['sample_tpath'];
            }
            if (! empty ( $downloadData ['link_tpath'] )) {
                $sampleNo = substr ( $key, 6 );
                $linkTpath [$sampleNo] = $downloadData ['link_tpath'];
            }
            if (! empty ( $downloadData ['slink_tpath'] )) {
                $sampleNo = substr ( $key, 9 );
                $slinkTpath [$sampleNo] = $downloadData ['slink_tpath'];
            }
        }
        
        /**
         * Getting downloadable product sample collection
         */
        $downloadableSample = Mage::getModel ( 'downloadable/sample' )->getCollection ()->addProductToFilter ( $downloadProductId )->addTitleToResult ( $store );
        
        Mage::getModel ( 'marketplace/download' )->deleteDownloadableSample ( $downloadableSample );
        
        /**
         * Getting downloadable product link collection
         */
        $downloadableLink = Mage::getModel ( 'downloadable/link' )->getCollection ()->addProductToFilter ( $downloadProductId )->addTitleToResult ( $store );
        
        Mage::getModel ( 'marketplace/download' )->deleteDownloadableLinks ( $downloadableLink );
        
        /**
         * Initilize Downloadable product data
         */
        $downloadableData = $this->getRequest ()->getPost ( 'downloadable' );
        try {
            /**
             * Storing Downloadable product sample data
             */
            Mage::getModel ( 'marketplace/download' )->saveDownLoadProductSample ( $downloadableData, $downloadProductId, $sampleTpath, $store );
            
            /**
             * Storing Downloadable product sample data
             */
            if (isset ( $downloadableData ['link'] )) {
                Mage::getModel ( 'marketplace/download' )->saveDownLoadProductLink ( $downloadableData, $downloadProductId, $linkTpath, $slinkTpath, $store );
            }
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
        }
    }
    
    /**
     * Update Quantity
     *
     * @param int $id
     *            Passed the product id to save the quantity
     * @param int $qty
     *
     * @return void
     */
    public function quantityeditAction() {

    	/**
    	 * Check license key
    	 */
    	$isInStock = '';
    	Mage::helper ( 'marketplace' )->checkMarketplaceKey ();
    	Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
   
    	/**
    	 * Check whether seller or not
    	*/
    	$this->checkWhetherSellerOrNot ();

    	$id = $this->getRequest ()->getParam ( 'id' );
    	$qty = $this->getRequest ()->getParam ( 'qty' );

    	$product = Mage::getModel ( 'catalog/product' )->load($id);
    	$stockData = $product->getStockData();
    	$stockData ['qty'] = $qty;
    	$stockData ['is_in_stock'] = '1';
    	$product->setStockData ( $stockData )->save();
    	 }
}