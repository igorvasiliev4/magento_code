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
 * This file is used get all seller information
 */
class Apptha_Marketplace_Block_Allseller extends Mage_Core_Block_Template {

    /**
     * Function to get all seller collection
     * 
     * @return \Apptha_Marketplace_Block_Allreview
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $seller_collection = $this->getallSeller();    
        $this->setCollection($seller_collection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')
                ->setCollection($seller_collection);
        $this->setChild('pager', $pager);
        $pager->setAvailableLimit(array(10 => 10, 20 => 20, 50 => 50));    
        return $this;
    }

    /**
     * Function to get pagination
     * 
     * Return pagination for collection
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * Function to get all seller collection
     * 
     * Return all seller data as array
     * @return array
     */
    function getallSeller() {        
        $tableName = Mage::getSingleton("core/resource")->getTableName('marketplace_sellerprofile');
        $model = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('customerstatus', 1);
        $model->getSelect()->join(array('t2' => $tableName),"e.entity_id = t2.seller_id and t2.store_title!=''", array('store_logo' => 't2.store_logo', 'store_title' => 't2.store_title'));
        return $model;
    }

}

