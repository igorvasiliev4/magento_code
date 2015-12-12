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
 * Compare Product Prices
 * This file is used to compare seller product price with others seller products
 */
class Apptha_Marketplace_Block_Compareprice extends Mage_Core_Block_Template {
 
 /**
  * Get Product Collection with 'compare_product_id' attribute filter
  *
  * Passed the product id for which we need to compare price
  * 
  * @param int $productId
  *         Return product collection as array
  * @return array
  */
 public function getComparePrice($productId) {
  $productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'is_assign_product', array (
    'eq' => 1 
  ) )->addAttributeToFilter ( 'visibility', array (
    'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE 
  ) )->addFieldToFilter ( 'assign_product_id', array (
    'eq' => $productId 
  ) );
  $productCollection->setOrder ( 'price', 'ASC' );
  return $productCollection;
 }
 
 /**
  * Get Review Collection of the particular seller
  *
  * Passed the seller id to get the review collection
  * 
  * @param int $sellerId
  *         Return the reviews count of particular seller
  * @return int
  */
 public function getReviewsCount($sellerId) {
  $storeId = Mage::app ()->getStore ()->getId ();
  $reviewsCollection = Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId );
  return $reviewsCollection->getSize ();
 }
 
 /**
  * Calculating average rating for each seller
  *
  * Passed the seller id to get the review collection
  * 
  * @param int $sellerId
  *         Return the average rating of particular seller
  * @return int
  */
 public function averageRatings($sellerId) {
  /**
   * Review Collection to retrive the ratings of the seller
   */
  $storeId = Mage::app ()->getStore ()->getId ();
  $reviews = Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId );
  /**
   * Calculate average ratings
   */
  $ratingsVal = array ();
  $avg = 0;
  if (count ( $reviews ) > 0) {
   foreach ( $reviews as $review ) {
    $ratingsVal [] = $review->getRating ();
   }
   /**
    * Calcualte count of ratings
    */
   $count = count ( $ratingsVal );
   /**
    * Calculate average ratings from count
    */
   $avg = array_sum ( $ratingsVal ) / $count;
  }
  return round ( $avg, 1 );
 }

}