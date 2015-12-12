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
 * Manage Products Grid
 */
class Apptha_Marketplace_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid {

 /**
  * Construct the inital display of grid information
  * Set the default sort for collection
  * Set the sort order as "DESC"
  *
  * Return array of data to display order information
  * 
  * @return array
  */
 public function __construct() {
  parent::__construct ();
  $this->setId ( 'productsGrid' );
  $this->setDefaultSort ( 'entity_id' );
  $this->setDefaultDir ( 'DESC' );
  $this->setSaveParametersInSession ( true );
 }
 
 /**
  * Get current store id
  *
  * Return current store id
  * 
  * @return type
  */
 protected function _getStore() {
  $storeId = ( int ) $this->getRequest ()->getParam ( 'store', 0 );
  return Mage::app ()->getStore ( $storeId );
 }
 
 /**
  * Function to get seller product collection
  *
  * Return the seller product collection information
  * return array
  */
 protected function _prepareCollection() {
  $store = $this->_getStore ();
  $sellerId = $this->getRequest ()->getParam ( 'id' );
  $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( 'sku' )->addAttributeToSelect ( 'name' )->addAttributeToSelect ( 'seller_id' )->addAttributeToSelect ( 'attribute_set_id' )->addAttributeToSelect ( 'type_id' );
  $getGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
  $collection->addAttributeToFilter ( 'group_id', array (
    'eq' => $getGroupId 
  ) );
  if ($sellerId != '') {
   $collection->addAttributeToFilter ( 'seller_id', array (
     'eq' => $sellerId 
   ) );
  }
  if (Mage::helper ( 'catalog' )->isModuleEnabled ( 'Mage_CatalogInventory' )) {
   $collection->joinField ( 'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left' );
  }
  if ($store->getId ()) {
   $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
   $collection->addStoreFilter ( $store );
   $collection->joinAttribute ( 'name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore );
   $collection->joinAttribute ( 'custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId () );
   $collection->joinAttribute ( 'status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId () );
   $collection->joinAttribute ( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId () );
   $collection->joinAttribute ( 'price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId () );
  } else {
   $collection->addAttributeToSelect ( 'price' );
   $collection->joinAttribute ( 'status', 'catalog_product/status', 'entity_id', null, 'inner' );
   $collection->joinAttribute ( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner' );
  }
  $this->setCollection ( $collection );
  parent::_prepareCollection ();
  $this->getCollection ()->addWebsiteNamesToResult ();
  return $this;
 }
 
 /**
  * Function to filter product according to website
  *
  * Return the filter product collection
  * return array
  */
 protected function _addColumnFilterToCollection($column) {  
   if ($this->getCollection () && $column->getId () == 'websites') {
    $this->getCollection ()->joinField ( 'websites', 'catalog/product_website', 'website_id', 'product_id=entity_id', null, 'left' );
   }  
  return parent::_addColumnFilterToCollection ( $column );
 }
 
 /**
  * Function to display fields with data
  *
  * Display information about orders
  * 
  * @return void
  */
 protected function _prepareColumns() {
  $this->addColumn ( 'entity_id', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'ID' ),
    'width' => '50px',
    'index' => 'entity_id' 
  ) );
  $this->addColumn ( 'name', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'Name' ),
    'index' => 'name' 
  ) );
  $store = $this->_getStore ();
  if ($store->getId ()) {
   $this->addColumn ( 'custom_name', array (
     'header' => Mage::helper ( 'catalog' )->__ ( 'Name in %s', $store->getName () ),
     'index' => 'custom_name' 
   ) );
  }
  $this->addColumn ( 'type', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'Type' ),
    'width' => '60px',
    'index' => 'type_id',
    'type' => 'options',
    'options' => Mage::helper ( 'marketplace' )->getProductTypes () 
  ) );
  $this->addColumn ( 'seller_id', array (
    'header' => Mage::helper ( 'marketplace' )->__ ( 'Seller Id' ),
    'width' => '80px',
    'index' => 'seller_id' 
  ) );
  $this->addColumn ( 'sellerid', array (
    'header' => Mage::helper ( 'marketplace' )->__ ( 'Seller Email' ),
    'width' => '150px',
    'index' => 'seller_id',
    'filter' => false,
    'sortable' => false,
    'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordersellerdetails' 
  ) );
  $this->addColumn ( 'sku', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'SKU' ),
    'width' => '80px',
    'index' => 'sku' 
  ) );
  $this->addColumn ( 'price', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'price' ),
    'type' => 'price',
    'currency_code' => $store->getBaseCurrency ()->getCode (),
    'index' => 'price' 
  ) );
  if (Mage::helper ( 'catalog' )->isModuleEnabled ( 'Mage_CatalogInventory' )) {
   $this->addColumn ( 'qty', array (
     'header' => Mage::helper ( 'catalog' )->__ ( 'Qty' ),
     'width' => '100px',
     'type' => 'number',
     'index' => 'qty' 
   ) );
  }
  $this->addColumn ( 'status', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'status' ),
    'width' => '70px',
    'index' => 'status',
    'type' => 'options',
    'options' => Mage::getSingleton ( 'catalog/product_status' )->getOptionArray () 
  ) );
  if (! Mage::app ()->isSingleStoreMode ()) {
   $this->addColumn ( 'websites', array (
     'header' => Mage::helper ( 'catalog' )->__ ( 'websites' ),
     'width' => '100px',
     'sortable' => false,
     'index' => 'websites',
     'type' => 'options',
     'options' => Mage::getModel ( 'core/website' )->getCollection ()->toOptionHash () 
   ) );
  }
  $this->addColumn ( 'action', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'Action' ),
    'width' => '50px',
    'type' => 'action',
    'getter' => 'getId',
    'actions' => array (
      array (
        'caption' => Mage::helper ( 'catalog' )->__ ( 'View' ),
        'url' => array (
          'base' => 'adminhtml/catalog_product/edit',
          'params' => array (
            'store' => $this->getRequest ()->getParam ( 'store' ) 
          ) 
        ),
        'field' => 'id' 
      ) 
    ),
    'filter' => false,
    'sortable' => false,
    'index' => 'stores' 
  ) );
  return parent::_prepareColumns ();
 }
 
 /**
  * Function for Mass action
  *
  * return void
  */
 protected function _prepareMassaction() {
  return $this;
 }
 
 /**
  * Function for link url
  *
  * Return the product edit page url
  * return string
  */
 public function getRowUrl($row) {
  return $this->getUrl ( 'adminhtml/catalog_product/edit', array (
    'store' => $this->getRequest ()->getParam ( 'store' ),
    'id' => $row->getId () 
  ) );
 }

}
