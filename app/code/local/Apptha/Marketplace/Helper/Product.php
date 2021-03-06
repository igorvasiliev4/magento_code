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
class Apptha_Marketplace_Helper_Product extends Mage_Core_Helper_Abstract {
    
    /**
     * Filter product collection by attributes
     */
    public function productFilterByAttribute($filterElement, $filterValue, $products) {
    	/** Get Product collection */
        $collection = $products;
        switch ($filterElement) {
            case 'entity_id' :
                if ($filterValue != '') {
                    $collection = $products->addAttributeToFilter ( 'entity_id', array (
                            'eq' => $filterValue 
                    ) );
                }
                break;
            case 'name' :
                if ($filterValue != '') {
                    $products->addAttributeToFilter ( 'name', array (
                            'like' => '%' . $filterValue . '%' 
                    ) );
                }
                break;
            case 'price' :
                if ($filterValue != '') {
                    $products->addAttributeToFilter ( 'price', array (
                            'eq' => $filterValue 
                    ) );
                }
                break;
            case 'status' :
                if ($filterValue != 0) {
                    $products->addAttributeToFilter ( 'status', array (
                            'eq' => $filterValue 
                    ) );
                }
                break;
            default :
                $collection = $products;
        }
        /** Return Collection */
        return $collection;
    }
    
    /**
     * Update assign product id while deleting assign product
     *
     * @param int $newAssignProductId            
     * @param string $assignProducts            
     */
    public function updateAssignProductId($newAssignProductId, $assignProducts) {
        if (! empty ( $newAssignProductId )) {
            $data = array (
                    'assign_product_id' => $newAssignProductId 
            );
            foreach ( $assignProducts as $assignProduct ) {
            	/** Get Product Id */
                if ($assignProduct->getEntityId ()) {
                	/** load entity id */
                    $model = Mage::getModel ( 'catalog/product' )->load ( $assignProduct->getEntityId () )->addData ( $data );
                    /** set Id */
                    $model->setId ( $assignProduct->getEntityId () )->save ();
                }
            }
            return true;
        }
    }
    
    /**
     *
     *
     * Change vacation mode
     *
     * @param int $vacationStatus            
     * @param int $disableProducts            
     * @param array $productId            
     * @param int $dateTo            
     * @param int $currentDate            
     * @return boolean
     */
    public function changevacationmode($vacationStatus, $disableProducts, $productId, $dateTo, $currentDate) {
        /** Disable Products */
        if ($disableProducts == 0) {
            $productStatus = 2;
        } else {
            $productStatus = 1;
        }
        /** Vacation Status */
        if ($vacationStatus == 0) {
        	/** ProductId Array */
            foreach ( $productId as $_productId ) {
                if (strtotime ( $dateTo ) >= strtotime ( $currentDate )) {
                    Mage::getModel ( 'catalog/product' )->load ( $_productId )->setStatus ( $productStatus )->save ();
                }
            }
        } else {
            foreach ( $productId as $_productId ) {
                if (strtotime ( $dateTo ) >= strtotime ( $currentDate )) {
                    Mage::getModel ( 'catalog/product' )->load ( $_productId )->setStatus ( 1 )->save ();
                }
            }
        }
        return true;
    }
    
    /**
     *
     *
     * Convert csv file to upload array
     *
     * @param string $csvFilePath            
     * @return array
     */
    public function convertCsvFileToUploadArray($csvFilePath) {
        $productInfo = array ();
        if (! empty ( $csvFilePath )) {
            /** Initializing new varien file */
            $csv = new Varien_File_Csv ();
            $data = $csv->getData ( $csvFilePath );
            $line = $lines = '';
            
            $keys = array_shift ( $data );
            
            /**
             * Getting instance for catalog product collection
             */
            $productInfo = $createProductData = array ();
            foreach ( $data as $lines => $line ) {
                if (count ( $keys ) == count ( $line )) {
                    $data [$lines] = array_combine ( $keys, $line );
                }
            }
            
            if (count ( $data ) <= 1 && count ( $keys ) >= 1 && ! empty ( $line ) && count ( $keys ) == count ( $line )) {
                $data [$lines + 1] = array_combine ( $keys, $line );
            }
            
            $createProductData = $this->uploadProductData ( $data );
            
            if (! empty ( $createProductData )) {
                $productInfo [] = $createProductData;
            }
        }
        
        return $productInfo;
    }
    
    /**
     *
     *
     * Upload product data
     *
     * @param array $data            
     * @return array $createProductData
     */
    public function uploadProductData($data) {
        $productInfo = $createProductData = array ();
        foreach ( $data as $value ) {
            if (isset ( $value ['sku'] )) {
                if (empty ( $value ['sku'] ) && $value ['sku'] != 0) {
                    $productInfo = Mage::helper ( 'marketplace/market' )->createUploadProductData ( $createProductData, $productInfo );
                    $createProductData = array ();
                    $createProductData = $value;
                } else {
                    $createProductData = array_merge_recursive ( $createProductData, $value );
                }
            }
        }
        return $createProductData;
    }
    
    /**
     *
     *
     * Unzip uploaded images
     *
     * @param string $ZipFileName            
     * @return boolean
     */
    public function exportZipFile($ZipFileName, $homeFolder) {
    	/** New ZIP archive */
        $zip = new ZipArchive ();
        if ($zip->open ( $ZipFileName ) === true) {
            /**
             * Make all the folders
             */
            for($i = 0; $i < $zip->numFiles; $i ++) {
                $OnlyFileName = $zip->getNameIndex ( $i );
                $FullFileName = $zip->statIndex ( $i );
                if ($FullFileName ['name'] [strlen ( $FullFileName ['name'] ) - 1] == "/") {
                    @mkdir ( $homeFolder . "/" . $FullFileName ['name'], 0700, true );
                }
            }
            
            /**
             * Unzip into the folders
             */
            for($i = 0; $i < $zip->numFiles; $i ++) {
                $OnlyFileName = $zip->getNameIndex ( $i );
                $FullFileName = $zip->statIndex ( $i );
                
                if (! ($FullFileName ['name'] [strlen ( $FullFileName ['name'] ) - 1] == "/") && preg_match ( '#\.(jpg|jpeg|gif|png)$#i', $OnlyFileName )) {
                    copy ( 'zip://' . $ZipFileName . '#' . $OnlyFileName, $homeFolder . "/" . $FullFileName ['name'] );
                }
            }
            $zip->close ();
        } else {
            Mage::getSingleton ( $this->strCoresession )->addError ( $this->__ ( "Error: Can't open zip file" ) );
        }
        return true;
    }
    
    /**
     *
     *
     * Get configurable product data
     *
     * @param int $sellerId            
     * @param int $configProductSellerId            
     * @param array $attributes            
     * @param array $productInfo            
     * @return array $configurableProductsData
     */
    public function getconfigurableProductsData($attributes, $productInfo, $configProduct) {
        
        $sellerId = '';
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        }
        $configurableProductsData = array ();
        /** Get Seller Id */
        $configProductSellerId = $configProduct->getSellerId ();
        if ($sellerId == $configProductSellerId && $sellerId != '' && is_array ( $attributes )) {
            foreach ( $attributes as $key => $attribute ) {
                $valueIndex = $pricingLabel = $pricingValue = '';
                $isPercentValue = 0;
                $valueIndex = $key;
                if (isset ( $productInfo ['option_price'] [$valueIndex] ) && $valueIndex != '') {
                    $isPercentValue = $this->getIsPercentValue ( $productInfo, $valueIndex );
                    $pricingValue = $this->getPricingValue ( $productInfo, $valueIndex );
                    $pricingLabel = $this->getPricingLabel ( $productInfo, $valueIndex );
                    $configurableProductsData = Mage::helper ( 'marketplace/market' )->createCongirurableProductData ( $configurableProductsData, $pricingValue, $attribute, $valueIndex, $pricingLabel, $isPercentValue );
                
                }
            }
        }
        /** Return Configurable Data */
        return $configurableProductsData;
    }
    
    /**
     * Get pricing label value
     *
     * @param array $productInfo            
     * @param number $valueIndex            
     * @return number $pricingLabel
     */
    public function getPricingLabel($productInfo, $valueIndex) {
        $pricingLabel = '';
        if (isset ( $productInfo ['label'] [$valueIndex] )) {
            $pricingLabel = $productInfo ['label'] [$valueIndex];
        }
        return $pricingLabel;
    }
    
    /**
     * Get pricing value for option
     *
     * @param array $productInfo            
     * @param number $valueIndex            
     * @return number $pricingValue
     */
    public function getPricingValue($productInfo, $valueIndex) {
        $pricingValue = '';
        if (isset ( $productInfo ['option_price'] [$valueIndex] )) {
            $pricingValue = $productInfo ['option_price'] [$valueIndex];
        }
        return $pricingValue;
    }
    
    /**
     * Get is percent value
     * 
     * @param array $productInfo            
     * @param number $valueIndex            
     * @return number$isPercentValue
     */
    public function getIsPercentValue($productInfo, $valueIndex) {
        $isPercentValue = 0;
        if (isset ( $productInfo ['option_price_mode'] [$valueIndex] )) {
            $isPercentValue = $productInfo ['option_price_mode'] [$valueIndex];
            if ($isPercentValue == 'percentage') {
                $isPercentValue = 1;
            } else {
                $isPercentValue = 0;
            }
        }
        return $isPercentValue;
    }
    
    /**
     *
     *
     * Assign configurable product data
     *
     * @param array $configAttributes            
     * @param array $configurableProductsData            
     * @param array $configProduct            
     * @return boolean
     */
    public function assignConfigurableProductData($configAttributes, $configurableProductsData, $configProduct) {
        $arrayCount = 0;
        foreach ( $configAttributes as $configAttribute ) {
            if (isset ( $configAttribute ['attribute_id'] )) {
                $configAttributeIdValue = $configAttribute ['attribute_id'];
                if (array_key_exists ( $configAttributeIdValue, $configurableProductsData )) {
                    $configAttributes = Mage::helper ( 'marketplace/market' )->createConfigAttributesForProudct ( $configAttribute, $configAttributes, $arrayCount, $configurableProductsData, $configAttributeIdValue );
                }
            }
            $arrayCount = $arrayCount + 1;
        }
        
        /**
         * Assign attribute values to configurable product
         */
        $configProduct->setConfigurableAttributesData ( $configAttributes );
        $configProduct->save ();
        
        return true;
    }
    
    /**
     * Get configurable attribute price value
     *
     * @param array $configAttributes            
     * @param array $configurableProductsData            
     * @param number $configAttributeIdValue            
     * @param number $valueIndex            
     * @param number $arrayCount            
     * @param number $arrayInnerCount            
     * @return array $configAttributes
     */
    public function getConfigAttributePriceValue($configAttributes, $configurableProductsData, $configAttributeIdValue, $valueIndex, $arrayCount, $arrayInnerCount) {
        if (isset ( $configurableProductsData [$configAttributeIdValue] [$valueIndex] ['pricing_value'] )) {
            $configAttributes [$arrayCount] ['values'] [$arrayInnerCount] ['pricing_value'] = $configurableProductsData [$configAttributeIdValue] [$valueIndex] ['pricing_value'];
        }
        return $configAttributes;
    }
    
    /**
     *
     *
     * Save configurable associate products
     *
     * @param array $selectedAssociateProductIds            
     * @param array $allSelectedUnSelectedAssociateProductIds            
     * @param array $configProduct            
     * @return boolean
     */
    public function saveConfigurableAssociateProduct($selectedAssociateProductIds, $allSelectedUnSelectedAssociateProductIds, $configProduct) {
        $unSelectedAssociateProductIds = array ();
        if (! empty ( $selectedAssociateProductIds )) {
            $unSelectedAssociateProductIds = array_diff ( $allSelectedUnSelectedAssociateProductIds, $selectedAssociateProductIds );
        } else {
            $unSelectedAssociateProductIds = $allSelectedUnSelectedAssociateProductIds;
        }
        /**
         * Assign simple product to configurable product
         */
        $usedSimpleProductIds = $configProduct->getTypeInstance ()->getUsedProductIds ();
        if (count ( $selectedAssociateProductIds ) >= 1) {
            $allAssociateProductIds = array_merge ( $usedSimpleProductIds, $selectedAssociateProductIds );
        } else {
            $allAssociateProductIds = $usedSimpleProductIds;
        }
        
        if (count ( $unSelectedAssociateProductIds ) >= 1) {
            $allSelectedAssociateProductIds = array_diff ( $allAssociateProductIds, $unSelectedAssociateProductIds );
        } else {
            $allSelectedAssociateProductIds = $allAssociateProductIds;
        }
        
        if (count ( $allSelectedAssociateProductIds ) >= 1) {
            Mage::getResourceModel ( 'catalog/product_type_configurable' )->saveProducts ( $configProduct, array_unique ( $allSelectedAssociateProductIds ) );
        } else {
            Mage::getResourceModel ( 'catalog/product_type_configurable' )->saveProducts ( $configProduct, array () );
        }
        return true;
    }
    
    /**
     * Save assign product data
     *
     * @param number $assignProductId            
     * @param array $productInfo            
     * @param array $product            
     * @param number $sellerId            
     * @param number $groupId            
     * @return boolean
     */
    public function saveassignproduct($assignProductId, $productInfo, $sellerId, $groupId, $product) {
        $shippingOption = $nationalShippingPrice = $internationalShippingPrice = $defaultCountry = '';
        $newProduct = Mage::getModel ( 'catalog/product' );
        $newProduct->setSku ( $productInfo ['sku'] );
        $newProduct->setStatus ( $productInfo ['status'] );
        $newProduct->setPrice ( $productInfo ['price'] );
        $newProduct->setTaxClassId ( 0 );
        $newProduct->setVisibility ( Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE );
        $newProduct->setIsAssignProduct ( 1 );
        $newProduct->setAssignProductId ( $assignProductId );
        $newProduct->setSpecialFromDate ( '' )->setSpecialToDate ( '' )->setSpecialPrice ( '' );
        $newProduct->setDescription ( $productInfo ['description'] );
        $newProduct->setShortDescription ( $productInfo ['description'] );
        $newProduct->setName ( $product->getName () );
        $newProduct->setTypeId ( $product->getTypeId () );
        $newProduct->setCategoryIds ( $product->getCategoryIds () );
        $newProduct->setAttributeSetId ( $product->getAttributeSetId () );
        $newProduct->setWebsiteIds ( $product->getWebsiteIds () );
        $newProduct->setStoreId ( 0 );
        $newProduct->setTaxClassId ( 0 );
        if ($product->getTypeId () == 'simple') {
            $newProduct->setWeight ( $product->getWeight () );
        }
        $newProduct->setImage ( $product->getImage () );
        $newProduct->setSmallImage ( $product->getSmallImage () );
        $newProduct->setThumbnail ( $product->getThumbnail () );
        if (isset ( $productInfo ['seller_shipping_option'] )) {
            $shippingOption = $productInfo ['seller_shipping_option'];
        }
        if (isset ( $productInfo ['national_shipping_price'] )) {
            $nationalShippingPrice = $productInfo ['national_shipping_price'];
        } else {
            $nationalShippingPrice = 0;
        }
        if (isset ( $productInfo ['international_shipping_price'] )) {
            $internationalShippingPrice = $productInfo ['international_shipping_price'];
        } else {
            $internationalShippingPrice = 0;
        }
        if (isset ( $productInfo ['default_country'] )) {
            $defaultCountry = $productInfo ['default_country'];
        }
        $newProduct->setSellerShippingOption ( $shippingOption );
        $newProduct->setNationalShippingPrice ( $nationalShippingPrice );
        $newProduct->setInternationalShippingPrice ( $internationalShippingPrice );
        $newProduct->setDefaultCountry ( $defaultCountry );
        $stockData = array ();
        
        $qty = $isInStock = 0;
        $qty = $productInfo ['stock_data'] ['qty'];
        $isInStock = $productInfo ['stock_data'] ['is_in_stock'];
        
        $stockData ['qty'] = $qty;
        $stockData ['is_in_stock'] = $isInStock;
        
        $newProduct->setStockData ( $stockData );
        
        /**
         * Assign create at time
         */
        $createdAt = Mage::getModel ( 'core/date' )->gmtDate ();
        
        /**
         * Initialize product create at time
         */
        if (isset ( $createdAt )) {
            $newProduct->setCreatedAt ( $createdAt );
        }
        /**
         * Initialize seller id
         */
        if (isset ( $sellerId )) {
            $newProduct->setSellerId ( $sellerId );
        }
        
        /**
         * Initialize group id
         */
        if (isset ( $groupId )) {
            $newProduct->setGroupId ( $groupId );
        }
        
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        $storeId = Mage::app ()->getStore ()->getStoreId ();
        
        $newProduct->save ();
        
        Mage::app ()->setCurrentStore ( $storeId );
        
        return $newProduct;
    }
    
    /**
     * Update assign product data
     * 
     * @param array $productInfo            
     * @param number $selectedProductId            
     */
    public function editassignproduct($productInfo, $selectedProductId) {
        $shippingOptionVar = $nationalShippingPriceVar = $internationalShippingPriceVar = $defaultCountryVar = '';
        
        $newProduct = Mage::getModel ( 'catalog/product' )->load ( $selectedProductId );
        $newProduct->setPrice ( $productInfo ['price'] );
        
        $newProduct->setDescription ( $productInfo ['description'] );
        $newProduct->setShortDescription ( $productInfo ['description'] );
        
        if (isset ( $productInfo ['seller_shipping_option'] )) {
            $shippingOptionVar = $productInfo ['seller_shipping_option'];
        }
        if (isset ( $productInfo ['national_shipping_price'] )) {
            $nationalShippingPriceVar = $productInfo ['national_shipping_price'];
        } else {
            $nationalShippingPriceVar = 0;
        }
        if (isset ( $productInfo ['international_shipping_price'] )) {
            $internationalShippingPriceVar = $productInfo ['international_shipping_price'];
        } else {
            $internationalShippingPriceVar = 0;
        }
        if (isset ( $productInfo ['default_country'] )) {
            $defaultCountryVar = $productInfo ['default_country'];
        }
        
        $newProduct->setSellerShippingOption ( $shippingOptionVar );
        $newProduct->setNationalShippingPrice ( $nationalShippingPriceVar );
        $newProduct->setInternationalShippingPrice ( $internationalShippingPriceVar );
        $newProduct->setDefaultCountry ( $defaultCountryVar );
        $newProduct->setSku ( $productInfo ['sku'] );
        
        $qty = $isInStock = 0;
        $qty = $productInfo ['stock_data'] ['qty'];
        $isInStock = $productInfo ['stock_data'] ['is_in_stock'];
        
        /**
         * Initilize product in stock
         */
        $isInStock = Mage::helper ( 'marketplace/marketplace' )->productInStock ( $isInStock );
        
        $newProduct->getResource ()->save ( $newProduct );
        
        $stockItem = Mage::getModel ( 'cataloginventory/stock_item' );
        $stockItem->assignProduct ( $newProduct );
        $stockItem->setData ( 'stock_id', 1 );
        $stockItem->setData ( 'qty', $qty );
        $stockItem->setData ( 'use_config_min_qty', 1 );
        $stockItem->setData ( 'use_config_backorders', 1 );
        $stockItem->setData ( 'min_sale_qty', 1 );
        $stockItem->setData ( 'use_config_min_sale_qty', 1 );
        $stockItem->setData ( 'use_config_max_sale_qty', 1 );
        $stockItem->setData ( 'is_in_stock', $isInStock );
        $stockItem->setData ( 'use_config_notify_stock_qty', 1 );
        $stockItem->setData ( 'manage_stock', 1 );
        $stockItem->save ();
        
        // This section is what was required.
        $stockStatus = Mage::getModel ( 'cataloginventory/stock_status' );
        $stockStatus->assignProduct ( $newProduct );
        $stockStatus->saveProductStatus ( $newProduct->getId (), 1 );
        
        return $newProduct;
    }
    
    /**
     * Send email notification to admin for assign new pproduct
     * 
     * @param number $sellerId            
     * @param array $product            
     */
    public function sentEmailToAdmin($sellerId, $product) {
        
        if (Mage::getStoreConfig ( 'marketplace/product/addproductemailnotification' ) == 1) {
            /**
             * Sending email for added new product
             */
            if (Mage::helper ( 'marketplace' )->getProductApproval () == 1) {
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductemailnotificationtemplate' );
            } else {
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductapprovalemailnotificationtemplate' );
            }
            $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
            $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
            $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
            
            /**
             * Selecting template id
             */
            
            if (Mage::helper ( 'marketplace' )->getProductApproval () == 1) {
                if ($templateId) {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_product_addproductemailnotificationtemplate' );
                }
            } else {
                if ($templateId) {
                    $emailTemplate = Mage::getModel ('core/email_template' )->load($templateId );
                } else {
                    $emailTemplate = Mage::getModel ('core/email_template')->loadDefault('marketplace_product_addproductapprovalemailnotificationtemplate' );
                }
            }
            $customer = Mage::getModel ( 'customer/customer' )->load ( $sellerId );
            $selleremail = $customer->getEmail ();
            $recipient = $toMailId;
            $sellername = $customer->getName ();
            $productname = $product->getName ();
            
            if (Mage::helper ( 'marketplace' )->getProductApproval () == 1) {
                $producturl = $product->getProductUrl ();
            } else {
                $producturl = Mage::helper ( 'adminhtml' )->getUrl ( 'adminhtml/catalog_product/edit', array (
                        'id' => $product->getId () 
                ) );
            }
            
            $emailTemplate->setSenderName ( $sellername );
            $emailTemplate->setSenderEmail ( $selleremail );
            $emailTemplateVariables = (array (
                    'ownername' => $toName,
                    'sellername' => $sellername,
                    'selleremail' => $selleremail,
                    'productname' => $productname,
                    'producturl' => $producturl 
            ));
            $emailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
            $emailTemplate->send ( $recipient, $sellername, $emailTemplateVariables );
        }
    }
    
    /**
     *
     *
     * Set product name info
     *
     * @param array $product            
     * @param string $name            
     * @return array
     */
    public function getProductNameInfo($product, $name) {
        /**
         * Initilize product weight
         */
        if (! empty ( $name )) {
            $product->setName ( $name );
        }
        return $product;
    }
    
    /**
     *
     *
     * Set product sku info
     *
     * @param array $product            
     * @param string $sku            
     * @return array
     */
    public function getProductSkuInfo($product, $sku) {
        /**
         * Initilize product weight
         */
        if (! empty ( $sku )) {
            $product->setSku ( $sku );
        }
        return $product;
    }
    
    /**
     *
     *
     * Set product weight info
     *
     * @param array $product            
     * @param string $sku            
     * @return array
     */
    public function getProductWeightInfo($product, $weight) {
        /**
         * Initilize product weight
         */
        if (! empty ( $weight )) {
            $product->setWeight ( $weight );
        }
        return $product;
    }
}