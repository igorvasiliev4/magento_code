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

$installer = $this;

/**
 *  @var $installer Mage_Core_Model_Resource_Setup */

/**
 * Load Initial setup
 */
$installer->startSetup ();

/**
 * Insert values into customer_group table
 * 
 * 
 */
$installer->run ( "INSERT INTO {$this->getTable('customer_group')} (`customer_group_code`,`tax_class_id`) values ('marketseller','3');" );

$setup = new Mage_Eav_Model_Entity_Setup ( 'core_setup' );
$setup->addAttribute ( 'customer', 'customerstatus', array (
        'label' => 'Customer Status',
        'visible' => true,
        'required' => false,
        'type' => 'varchar',
        'input' => 'select',
        'source' => 'marketplace/entity_attribute_source_table' 
) );

$eavConfig = Mage::getSingleton ( 'eav/config' );
$attribute = $eavConfig->getAttribute ( 'customer', 'customerstatus' );
$attribute->setData ( 'used_in_forms', array (
        'adminhtml_customer',
        'customer_account_create',
        'customer_account_edit' 
) );
$attribute->save ();
/**
 * create customerid
 */
$setup->addAttribute ( 'catalog_product', 'seller_id', array (
        'group' => 'Special Attributes',
        'input' => 'text',
        'type' => 'text',
        'label' => 'Customer Id',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 1,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'is_html_allowed_on_front' => 0,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL 
) );

/**
 * create Groupid
 */

$setup->addAttribute ( 'catalog_product', 'group_id', array (
        'group' => 'Special Attributes',
        'input' => 'text',
        'type' => 'text',
        'label' => 'Group Id',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 1,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'is_html_allowed_on_front' => 0,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL 
) );

/**
 * Table structure for table `marketplace_commission`
 */

$installer->run ( "
  DROP TABLE IF EXISTS {$this->getTable('marketplace_commission')};

  CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_commission')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_qty` decimal(12,0) NOT NULL,
  `product_amt` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `increment_id` int(11) NOT NULL,
  `commission_fee` decimal(12,4) NOT NULL,
  `seller_amount` decimal(12,4) NOT NULL,
  `order_total` decimal(12,4) NOT NULL,
  `order_status` varchar(30) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `credited` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 " );

/**
 * Table structure for table `marketplace_transaction`
 */

$installer->run ( "
   DROP TABLE IF EXISTS {$this->getTable('marketplace_transaction')};

   CREATE TABLE IF NOT EXISTS  {$this->getTable('marketplace_transaction')}  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commission_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `seller_commission` decimal(12,4) NOT NULL,
  `admin_commission` decimal(12,4) NOT NULL,
  `order_id` int(11) NOT NULL,
  `paid` tinyint(4) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `paid_date` datetime NOT NULL,
  `comment` text NOT NULL,
  `received_status` tinyint(4) NOT NULL,
  `acknowledge_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8" );

/**
 * Table structure for table `marketplace_sellerprofile`
 */

$installer->run ( "
 DROP TABLE IF EXISTS {$this->getTable('marketplace_sellerprofile')};

 CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_sellerprofile')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `store_title` varchar(50) NOT NULL,
  `state` varchar(25) NOT NULL,
  `country` varchar(25) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `store_banner` varchar(150) NOT NULL,
  `store_logo` varchar(150) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `meta_description` varchar(150) NOT NULL,
  `meta_keyword` varchar(150) NOT NULL,
  `twitter_id` varchar(100) NOT NULL,
  `facebook_id` varchar(100) NOT NULL,
  `google_id` varchar(100) NOT NULL,
  `linked_id` varchar(100) NOT NULL,
  `bank_payment` varchar(250) NOT NULL,
  `paypal_id` varchar(35) NOT NULL,
  `show_profile` tinyint(4) NOT NULL,
  `commission` decimal(12,2) NOT NULL,
  `setbanner` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8" );

/**
 * Table structure for table `marketplace_sellerreview`
 */

$installer->run ( "
DROP TABLE IF EXISTS {$this->getTable('marketplace_sellerreview')};

CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_sellerreview')} (
  `seller_review_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `store_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`seller_review_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8" );
/**
 * Closing Installer set up
 */
$installer->endSetup ();
