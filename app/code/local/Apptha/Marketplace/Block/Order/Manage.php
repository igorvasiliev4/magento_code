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
 * Manage order information
 * Manage order information with seller details and also with pagination
 */
class Apptha_Marketplace_Block_Order_Manage extends Mage_Core_Block_Template {
    
    /**
     * Collection for manage orders
     *
     * @return \Apptha_Marketplace_Block_Order_Manage
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        /** 
         * Get Seller Orders
         */
        $manageCollection = $this->getsellerOrders ();
        $this->setCollection ( $manageCollection );
        /** 
         * Get Layout
         */
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $manageCollection );
        $pager->setAvailableLimit ( array (
                10 => 10,
                20 => 20,
                50 => 50 
        ) );
        /**
         * Set pager for manage order page
         */
        $this->setChild ( 'pager', $pager );
        return $this;
    }
    
    /**
     * Function to get pagination
     *
     * Return pagination for collection
     *
     * @return array
     */
    public function getPagerHtml() {
    	/** 
    	 * Get Child Html
    	 */
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Function to get seller order details
     *
     * Return seller orders information
     *
     * @return array
     */
    public function getsellerOrders() {
    $data = $status = $selectFilter = $from = $to = '';
    $data = $this->getRequest()->getPost();
    if (isset($data['status'])) {
    	$status = $data['status'];
    }
    if (isset($data['select_filter'])) {
    	$selectFilter = $data['select_filter'];
    }
    if (!empty($selectFilter)) {
    	switch ($selectFilter) {
    		case "today":
    			/**
    			 * today interval
    			 */
    			$startDay = strtotime("-1 today midnight");
    			$endDay = strtotime("-1 tomorrow midnight");
    			$from = date("Y-m-d", $startDay);
    			$to = date("Y-m-d", $endDay);
    			$fromDisplay = date("Y-m-d", $startDay);
    			$toDisplay = date("Y-m-d", $startDay);
    			break;
    		case "yesterday":
    			/**
    			 *  yesterday interval
    			 */
    			$startDay = strtotime("-1 yesterday midnight");
    			$endDay = strtotime("-1 today midnight");
    			$from = date("Y-m-d", $startDay);
    			$to = date("Y-m-d", $endDay);
    			$fromDisplay = date("Y-m-d", $startDay);
    			$toDisplay = date("Y-m-d", $startDay);
    			break;
    		case "lastweek":
    			/**
    			 *  last week interval
    			 */
    			$to = date('d-m-Y');
    			$toDay = date('l', strtotime($to));
    			/**
    			 *  if today is monday, take last monday
    			*/
    			if ($toDay == 'Monday') {
    				$startDay = strtotime("-1 monday midnight");
    				$endDay = strtotime("yesterday");
    			} else {
    				$startDay = strtotime("-2 monday midnight");
    				$endDay = strtotime("-1 sunday midnight");
    			}
    			$from = date("Y-m-d", $startDay);
    			$to = date("Y-m-d", $endDay);
    			$to = date('Y-m-d', strtotime($to . ' + 1 day'));
    			$fromDisplay = $from;
    			$toDisplay = date("Y-m-d", $endDay);
    			break;
    		case "lastmonth":
    			/**
    			 *  last month interval
    			 */
    			$from = date('Y-m-01', strtotime('last month'));
    			$to = date('Y-m-t', strtotime('last month'));
    			$to = date('Y-m-d', strtotime($to . ' + 1 day'));
    			$fromDisplay = $from;
    			$toDisplay = date('Y-m-t', strtotime('last month'));
    			break;
    		case "custom":
    			/**
    			 *  last custom interval
    			 */
    			$from = date('Y-m-d', strtotime($data['date_from']));
    			$to = date('Y-m-d', strtotime($data['date_to'] . ' + 1 day'));
    			$fromDisplay = $from;
    			$toDisplay = date('Y-m-d', strtotime($data['date_to']));
    			break;
    	}
    }
  
    $dbFrom = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
    $dbTo = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
    $orders = Mage::getModel('marketplace/commission')->getCollection();
    $orders->addFieldToSelect('*');
    $orders->addFieldToFilter('seller_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
    $orders ->getSelect()
    ->columns('SUM(seller_amount) as seller_amount')
    ->group('order_id');
    if ($status != '') {
    	$orders->addFieldToFilter('order_status', array('in' => array($status)));
    }
    if ($selectFilter != '') {
    	$orders->addFieldToFilter('created_at', array('from' => $dbFrom, 'to' => $dbTo));
    }
    $orders->setOrder('order_id', 'desc');
    return $orders;
}
    
    /**
     * Get seller products by order id
     *
     * @param number $getOrderId
     * @param number $getSellerId
     */
    public function getProductDetails($getOrderId,$getSellerId){
    	/**
    	 * Getting seller product ids from order
    	 */
    	$products = Mage::getModel('marketplace/commission')->getCollection();
    	$products->addFieldToSelect('*');
    	$products->addFieldToFilter('order_id',$getOrderId);
    	$products->addFieldToFilter('seller_id',$getSellerId);
    	$productIds = array_unique($products->getColumnValues('product_id'));
    
    	/**
    	 * Getting seller order product names
    	 */
    	$productsCollection = Mage::getModel('catalog/product')
    	->getCollection()
    	->addAttributeToSelect(array('name'))
    	->addAttributeToFilter('entity_id', array('in' => $productIds));
    	$productNames = array_unique($productsCollection->getColumnValues('name'));
    	/**
    	 * Return seller product names in particualr order
    	 */
    	return $productNameString = implode(',',$productNames);
    }
}

