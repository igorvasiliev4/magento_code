<?php
$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `stripe_cc_expmo` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `stripe_cc_expyr` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `stripe_cc_number` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `stripe_cc_type` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `stripe_cc_cvc` VARCHAR( 255 ) NOT NULL ;



ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `stripe_cc_expmo` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `stripe_cc_expyr` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `stripe_cc_number` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `stripe_cc_type` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `stripe_cc_cvc` VARCHAR( 255 ) NOT NULL ;

");

$installer->endSetup();
