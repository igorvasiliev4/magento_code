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
 * Renderer to display ordered product information
 */
class Apptha_Marketplace_Block_Adminhtml_Renderersource_OrderProductdetails extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Function to get product order details
     * 
     * Return the product order details
     * @return string
     */
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $commission = Mage::getModel('marketplace/commission')->load($value);
        $getProductId = $commission->getProductId();
        $itemItem = Mage::getModel('catalog/product')->load($getProductId);
        $pId = Mage::helper("adminhtml")->getUrl('adminhtml/catalog_product/edit', array('id' => $getProductId));
        return '<a href="' . $pId . '" target="_blank" >' . $itemItem->getName() . '</a>  X ' . '<strong>' . ($commission->getProductQty()) . '</strong>';
    }

}

