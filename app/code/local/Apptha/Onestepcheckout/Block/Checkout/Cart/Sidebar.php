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
 * Cart Side bar
 * This class is used for the checkout cart display in sidebar
 */
class Apptha_OneStepCheckout_Block_Checkout_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar {
 /**
  * Redirect to onestepcheck url if enabled
  *
  * @return string
  */
 public function getCheckoutUrl() {
  if (! $this->helper ( 'onestepcheckout' )->isOnepageCheckoutLinksEnabled ()) {
   return parent::getCheckoutUrl ();
  }
  return $this->getUrl ( 'onestepcheckout', array (
    '_secure' => true 
  ) );
 }
}
