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
 * Customer Review
 * This file is used to get the customer review list functionality
 */
class Apptha_Marketplace_Block_Customerreview extends Mage_Core_Block_Template{
    /**
     * Function to get all review collection with pagination
     * 
     * @return Apptha_Marketplace_Block_Customerreview 
     */
       protected function _prepareLayout(){
         
          
        parent::_prepareLayout();
        $reviewCollection = $this->getCustomer();            
        $this->setCollection($reviewCollection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')                   
                ->setCollection($reviewCollection);
        $pager->setAvailableLimit(array(10 => 10,20 => 20,30=>30,50=>50));
        $pager->setLimit(10);
        $this->setChild('pager', $pager);
        return $this;
    }
    /**
     * Function to get pagination
     * 
     * Return pagination for collection
     * @return array
     */
    public function getPagerHtml(){
        return $this->getChildHtml('pager');
    }
    /**
     * Function to get all review collection
     * 
     * Return customer review collection as array
     * @return array
     */
    function getCustomer(){
       if (Mage::getSingleton('customer/session')->isLoggedIn()) {
           $customer    = Mage::getSingleton('customer/session')->getCustomer();
           $id          = $customer->getId();
           $storeId    = Mage::app()->getStore()->getId();
           $collection  = Mage::getModel('marketplace/sellerreview')
                            ->getCollection()               
                            ->addFieldToFilter('status',1)
                             ->addFieldToFilter('store_id',$storeId)
                            ->addFieldToFilter('customer_id',$id);
       }     
       return $collection; 
   } 
} 
