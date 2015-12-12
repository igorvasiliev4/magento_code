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
 * This file is used to create attribute group
 */
$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();
/**
 * Adding attribute group
 */

//Mage_Eav_Model_Entity_Setup   addAttributeGroup  (mixed $entityTypeId, mixed $setId, string $name, [int $sortOrder = null]) 
$setup->addAttributeGroup('catalog_product', 'Default', 'Custom Attribute', 1000);
 
$installer->endSetup();