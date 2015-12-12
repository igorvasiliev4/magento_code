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
 * Seller vacation mode
 * Getting seller vacation information
 */
class Apptha_Marketplace_Block_Vacationmode extends Mage_Core_Block_Template {

    /**
     * Load vacation information by default in vacation form if seller already seller submit the vacation form
     * 
     * Return the seller vacation information
     * @return array
     */
    function loadVactionInfo() {
        $seller = Mage::getSingleton('customer/session')->getCustomer();
        $sellerId = $seller->getId();
        return Mage::getModel('marketplace/vacationmode')->load($sellerId, 'seller_id');       
    }

}

