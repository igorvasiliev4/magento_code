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
 * View order information
 */
class Apptha_Marketplace_Block_Adminhtml_Orderview_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to view order information
     *
     * @return array
     */
    public function __construct() {
        parent::__construct ();
        /**Set Id */
        $this->setId ( 'orderviewGrid' );
        /** Set Entity Id */
        $this->setDefaultSort ( 'entity_id' );
        /** Set Default desc */
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
    }
    
    /**
     * Function to get order collection
     *
     * Return the seller product's order information
     * return array
     */
    protected function _prepareCollection() {
    	/** Commission Get Collection */
        $orders = Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToSelect ( '*' )->addFieldToFilter ( 'order_status', array (
                'eq' => 'complete' 
        ) )->addFieldToFilter ( 'status', array (
                'eq' => 1 
        ) )->setOrder ( 'order_id', 'desc' );
        /** Set Collection */
        $this->setCollection ( $orders );
        return parent::_prepareCollection ();
    }
    
    /**
     * Function to create custom column
     *
     * @param string $id            
     * @return string colunm value
     */
    public function createCustomColumn($id, $store) {
        switch ($id) {
            case 'Seller detail' :
               $value= $this->getSellerDetail();
                break;
            case 'Product details' :
              $value= $this->getProductDetail();
                break;
            case 'Product Price' :
               $value= $this->getProductPrice($store);
                break;
            default :
                $value = '';
        }
        return $value;
    }
    
    /**
     * Function to display fields with data
     *
     * Display information about orders
     *
     * @return void
     */
    protected function _prepareColumns() {
    	/** Get Store */
        $store = Mage::app ()->getStore ();
        $this->createCustomColumn ( 'Seller detail', $store );
        $incrementId = array (
                'header' => Mage::helper ( 'sales' )->__ ( 'Order #' ),
                'width' => '100px',
                'index' => 'increment_id' 
        );
        $this->addColumn ( 'increment_id', $incrementId );
        /** Create Custom Column */
        $this->createCustomColumn ( 'Product details', $store );
        /** Create Product Price */
        $this->createCustomColumn ( 'Product Price', $store );
        
        
        $this->getFields($store);
        
        /**
         * View Action
         */
        $actions = array (
                'caption' => Mage::helper ( 'marketplace' )->__ ( 'View' ),
                'url' => array (
                        'base' => 'adminhtml/sales_order/view/' 
                ),
                'field' => 'order_id' 
        );
        $this->addColumn ( 'view', array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'View' ),
                'type' => 'action',
                'getter' => 'getOrderId',
                'actions' => array (
                        $actions 
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true 
        ) );
        return parent::_prepareColumns ();
    }
    
    /**
     * Function for Mass action(credit payment to seller)
     *
     * Will change the credit order status of the seller
     * return void
     */
    protected function _prepareMassaction() {
    	/** set mass action id */
        $this->setMassactionIdField ( 'id' );
        $formFieldName = 'marketplace';
        /** get Mass action block */
        $this->getMassactionBlock ()->setFormFieldName ( $formFieldName );
        $lable = Mage::helper ( 'marketplace' )->__ ( 'Credit' );
        $url = $this->getUrl ( '*/*/masscredit' );
        $this->getMassactionBlock ()->addItem ( 'credit', array (
                'label' => $lable,
                'url' => $url 
        ) );
        return $this;
    }
    
    /**
     * Function for link url
     *
     * Not redirected to any page
     * return void
     */
    public function getRowUrl($row) {
        $rowFlag = '';
        if (! empty ( $row )) {
            $rowFlag = false;
        }
        return $rowFlag;
    }
    /**
     * Function for adding seller detail column
     *
     * Not redirected to any page
     * return void
     */
    public function getSellerDetail(){
        $sellerEmail = array (
                'header' => Mage::helper ( 'sales' )->__ ( 'Seller detail' ),
                'width' => '150px',
                'index' => 'seller_id',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordersellerdetails'
        );
     return     $this->addColumn ( 'selleremail', $sellerEmail );
        
    }
    /**
     *Function for adding product detail column
     *
     *
     * Not redirected to any page
     * return void
     */
    public function getProductDetail(){
        $productDetails = array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Product details' ),
                'width' => '150px',
                'index' => 'id',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_OrderProductdetails'
        );
       return   $this->addColumn ( 'productdetail', $productDetails );
        
       
    }
    /**
     *Function for getting product price
     *
     *
     * Not redirected to any page
     * return void
     */
   public function getProductPrice($store){
       $productAmt = array (
               'header' => Mage::helper ( 'sales' )->__ ( 'Product Price' ),
               'align' => 'right',
               'index' => 'product_amt',
               'width' => '80px',
               'type' => 'price',
               'currency_code' => $store->getBaseCurrency ()->getCode (),
               'currency' => 'order_currency_code'
       );
      return   $this->addColumn ( 'product_amt', $productAmt );
       } 
    
public function getFields($store){
    $sellerAmount = array (
            'header' => Mage::helper ( 'sales' )->__ ( 'Seller\'s Earned Amount' ),
            'align' => 'right',
            'index' => 'seller_amount',
            'width' => '80px',
            'type' => 'price',
            'currency_code' => $store->getBaseCurrency ()->getCode (),
            'currency' => 'order_currency_code'
    );
    $this->addColumn ( 'seller_amount', $sellerAmount );
    $commissionFee = array (
            'header' => Mage::helper ( 'sales' )->__ ( 'Commission Fee' ),
            'align' => 'right',
            'index' => 'commission_fee',
            'width' => '80px',
            'type' => 'price',
            'currency_code' => $store->getBaseCurrency ()->getCode (),
            'currency' => 'order_currency_code'
    );
    $this->addColumn ( 'commission_fee', $commissionFee );
    $orderCreatedAt = array (
            'header' => Mage::helper ( 'marketplace' )->__ ( 'Order At' ),
            'align' => 'center',
            'width' => '200px',
            'index' => 'order_id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Orderdate'
    );
    $this->addColumn ( 'order_created_at', $orderCreatedAt );
    /**
     * Credit Action
    */
    $action = array (
            'header' => Mage::helper ( 'marketplace' )->__ ( 'Actions' ),
            'align' => 'center',
            'width' => '100px',
            'index' => 'id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordercredit'
    );
    $this->addColumn ( 'action', $action );
    /**
     * Payment status
    */
    $paymentStatus = array (
            'header' => Mage::helper ( 'marketplace' )->__ ( 'Ack Status' ),
            'align' => 'center',
            'width' => '100px',
            'index' => 'payment_status',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Receivedstatus'
    );
    $this->addColumn ( 'payment_status', $paymentStatus );
    /**
     * Acknowledge Date
    */
    $acknowledgeDate = array (
            'header' => Mage::helper ( 'marketplace' )->__ ( 'Ack On' ),
            'align' => 'center',
            'width' => '100px',
            'index' => 'acknowledge_date',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Acknowledgedate'
    );
    $this->addColumn ( 'acknowledge_date', $acknowledgeDate );
}
   
    
}

