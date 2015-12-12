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
 * Manage seller products functionality
 */
class Apptha_Marketplace_Block_Product_Assignproduct extends Mage_Core_Block_Template {
 
 /**
  * Collection for manage products
  *
  * @return \Apptha_Marketplace_Block_Product_Manage
  */
 protected function _prepareLayout() {
  parent::_prepareLayout ();
  $manageAssignProductCollection = $this->manageProducts ();
  $this->setCollection ( $manageAssignProductCollection );
  $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $manageAssignProductCollection );
  $pager->setAvailableLimit ( array (
    10 => 10,
    20 => 20,
    50 => 50 
  ) );
  $this->setChild ( 'pager', $pager );
  return $this;
 }
 
 /**
  * Function to get the product details
  *
  * Return product collection
  * 
  * @return array
  */
 public function manageProducts() {
  
  $filterName = $this->getRequest ()->getParam ( 'filter_name' );
  $products = Mage::getModel ( 'catalog/product' )->getCollection ();
  $products->addAttributeToSelect ( '*' );
  $getFilterName = Mage::getSingleton ( 'core/session' )->getFilterNameForPagination ();
  /**
   * Check filter name and session pagination filter name are not empty
   */
  if (! empty ( $filterName ) || ! empty ( $getFilterName )) {
   if ($filterName != '') {
    $products->addAttributeToFilter ( 'name', array (
      'like' => '%' . $filterName . '%' 
    ) );
    $getFilterName = Mage::getSingleton ( 'core/session' )->setFilterNameForPagination ( $filterName );
   } else {
    $products->addAttributeToFilter ( 'name', array (
      'like' => '%' . $getFilterName . '%' 
    ) );
   }
  } else {
   $products->addAttributeToFilter ( 'entity_id', 0 );
  }
  /**
   * Get Customer id
   */
  $cusId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
  $products->addAttributeToFilter ( 'seller_id', array (
    'neq' => $cusId 
  ) );
  $products->addAttributeToFilter ( 'is_assign_product', array (
    'neq' => 1 
  ) );
  $products->addAttributeToFilter ( 'visibility', array (
    2,
    3,
    4 
  ) );
  $products->addAttributeToFilter ( 'type_id', array (
    'neq' => 'configurable' 
  ) );
  $products->addAttributeToSort ( 'entity_id', 'DESC' );
  return $products;
 }
 
 /**
  * Function to display pagination
  *
  * Return collection with pagination
  * 
  * @return array
  */
 public function getPagerHtml() {
  return $this->getChildHtml ( 'pager' );
 }
 
 /**
  * Function to get multi select url
  *
  * Return the multi select option url
  * 
  * @return string
  */
 public function getmultiselectUrl() {
  return Mage::getUrl ( 'marketplace/sellerproduct/assignproduct' );
 }
}