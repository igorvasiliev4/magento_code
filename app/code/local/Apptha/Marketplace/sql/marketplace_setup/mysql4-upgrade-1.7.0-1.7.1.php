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
* This file is used to create table for special price approve/disapprove
*/

/**
 *  @var $installer Mage_Core_Model_Resource_Setup */

/**
 * Load Initial setup
 */
$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup ( 'core_setup' );
//$setup->removeAttribute( 'catalog_product', 'show_special_price' );


$setup->addAttribute ( 'catalog_product', 'show_special_price', array (
		'group' => 'Prices',
		'type' => 'int',
		'backend' => '',
		'frontend_input' => '',
		'frontend' => '',
		'label' => 'Show Special Price',
		'input' => 'select',
		'default' => array (0),
		'class' => '',
		'source' => 'eav/entity_attribute_source_boolean',
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => true,
		'frontend_class' => '',
		'required' => false,
		'user_defined' => true,
		'default' => '',
		'position' => 100
) );

$installer->endSetup();

