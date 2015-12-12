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
 * Toggle menu in header on hover
 */
class Apptha_CustomMenu_Block_Toggle extends Mage_Core_Block_Template {
 
 /**
  * On page load header menu will be displayed
  *
  * Return the menu content(text)
  * 
  * @return string
  */
 public function _prepareLayout() {
  /**
   * check condition custom genral menu is enable or not
   * @return empty
   */
  if (! Mage::getStoreConfig ( 'custom_menu/general/enabled' ) || Mage::helper('custommenu')->isMobile()) {
   return;
  }
  $layout = $this->getLayout ();
  $topnav = $layout->getBlock ( 'catalog.topnav' );
  /**
   * check condition topnav value is object
   */
  if (is_object ( $topnav )) {
   $topnav->setTemplate ( 'custommenu/top.phtml' );
   $head = $layout->getBlock ( 'head' );
   $head->addItem ( 'skin_js', 'js/custommenu/webtoolkit.base64.js' );
   $head->addItem ( 'skin_js', 'js/custommenu/custommenu.js' );
   $head->addItem ( 'skin_css', 'css/custommenu.css' );
   /**
    * Insert menu content
    */
   if (! Mage::getStoreConfig ( 'custom_menu/general/ajax_load_content' )) {
    $menuContent = $layout->getBlock ( 'custommenu-content' );
    /**
     * check condition menu content is object
     */
    if (! is_object ( $menuContent )) {
     $menuContent = $layout->createBlock ( 'core/template', 'custommenu-content' )->setTemplate ( 'custommenu/menucontent.phtml' );
    }
    $positionTarget = $layout->getBlock ( 'before_body_end' );
    /**
     * check condition position target is object
     */
    if (is_object ( $positionTarget )) {
     $positionTarget->append ( $menuContent );
    }
   }
  }
 }
}
