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
 * Manage seller reviews and ratings
 */
class Apptha_Marketplace_Block_Adminhtml_Sellerreview_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection 
     * Set the sort order as "DESC"
     * 
     * Return array of data to view order information
     * @return array
     */
    public function __construct() {
        parent::__construct();
        $this->setId('sellerreviewGrid');
        $this->setDefaultSort('seller_review_id' );
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Function to get review collection
     * 
     * Return the seller review information
     * return array
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('marketplace/sellerreview')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Function to display fields with data
     * 
     * Display information about orders 
     * @return void
     */
    protected function _prepareColumns() {
        $this->addColumn('seller_review_id' , array(
            'header' => Mage::helper('customer')->__('Review ID'),
            'width' => '40px',
            'index' => 'seller_review_id' ,
        ));
        $this->addColumn('customer_at', array(
            'header' => Mage::helper('customer')->__('Reviewed On'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true
        ));
        $reviewArr = array('header' => Mage::helper('customer')->__('Review'),'type' => 'text','align' => 'left','index' => 'review','width' => '250px',);
        $this->addColumn('review', $reviewArr);
        $ratingArr = array('header' => Mage::helper('customer')->__('Rating'),'type' => 'text','align' => 'center','index' => 'rating',);
        $this->addColumn('rating',$ratingArr);
        $customerId = array('header' => Mage::helper('customer')->__('Customer ID'),'type' => 'text','align' => 'center','index' => 'customer_id',);
        $this->addColumn('customer_id',$customerId);
        $productId = array('header' => Mage::helper('customer')->__('Product ID'),'type' => 'text','align' => 'center','index' => 'product_id',);
        $this->addColumn('product_id', $productId);
        $sellerId = array('header' => Mage::helper('customer')->__('Seller ID'),'type' => 'text','align' => 'center','index' => 'seller_id',);
        $this->addColumn('seller_id',$sellerId);
        $status = array('header' => Mage::helper('marketplace')->__('Status'),'width' => '150px','type' => 'options','index' => 'status',
            'options' => Mage::getSingleton('marketplace/status_reviewstatus')->getOptionArray());
        $this->addColumn('status', $status);
        /**
         * Action to change the review status
         */
        $this->addColumn('action', array(
            'header' => Mage::helper('marketplace')->__('Action'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('marketplace')->__('Pending'),
                    'url' => array('base' => '*/*/pending'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('marketplace')->__('Approve'),
                    'url' => array('base' => '*/*/approve'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('marketplace')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false
        ));
        return parent::_prepareColumns();
    }

    /**
     * Function for Mass action(approve or delete)
     * 
     * Will change the review status of the seller
     * return void
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('marketplace');
        $this->getMassactionBlock()->addItem('disapprove', array(
            'label' => Mage::helper('customer')->__('Pending'),
            'url' => $this->getUrl('*/*/massPending')
        ));
        $this->getMassactionBlock()->addItem('approve', array(
            'label' => Mage::helper('marketplace')->__('Approve'),
            'url' => $this->getUrl('*/*/massApprove')
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('marketplace')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('marketplace')->__('Are you sure?')
        ));
        return $this;
    }

    /**
     * Function for link url
     * 
     * Not redirected to any page
     * return void
     */
    public function getRowUrl($row) {
    if(!empty($row)){
    $row = false;
    }
    return $row;
    }

}

