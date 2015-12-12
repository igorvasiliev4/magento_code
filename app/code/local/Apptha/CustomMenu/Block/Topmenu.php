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
 * @version     1.2.3
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
/**
 * If custom menu module is enabled the class
 * "Apptha_CustomMenu_Block_Topmenu" will be extended "Mage_Page_Block_Html_Topmenu"
 */
if (! Mage::getStoreConfig ( 'custom_menu/general/enabled' ) || Mage::helper('custommenu')->isMobile()) {
 class Apptha_CustomMenu_Block_Topmenu extends Mage_Page_Block_Html_Topmenu {
 }
 
 return;
}

/**
 * If custom menu module is not enabled the class
 * "Apptha_CustomMenu_Block_Topmenu" will be extended "Apptha_CustomMenu_Block_Navigation"
 */
class Apptha_CustomMenu_Block_Topmenu extends Apptha_CustomMenu_Block_Navigation {
}
