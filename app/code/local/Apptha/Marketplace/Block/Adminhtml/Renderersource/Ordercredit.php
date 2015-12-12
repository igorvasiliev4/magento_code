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
 * Credit status change
 * Renderer to change the credit status from 'credit' to 'credited'
 */
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordercredit extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Function to change the crdit status from 'credit' to 'credited'
     * 
     * Return the status
     * @return varchar
     */
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $commissionDetails = Mage::getModel('marketplace/commission')->load($value);
        $getCredited = $commissionDetails->getCredited();
        if (empty($getCredited)) {
            $result = "<a href='" . $this->getUrl('*/*/credit', array('id' => $value)) . "' title='" . Mage::helper('marketplace')->__('click to Credit') . "'>" . Mage::helper('marketplace')->__('Credit') . "</a>";
        } else {
            $result = Mage::helper('marketplace')->__('Credited');
        }
        return $result;
    }

}

