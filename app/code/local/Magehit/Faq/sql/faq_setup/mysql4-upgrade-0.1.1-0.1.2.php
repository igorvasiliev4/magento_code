<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('faq')}	
	ADD COLUMN `order` varchar(255) NOT NULL default '' AFTER `faq_id`;
ALTER TABLE {$this->getTable('faqitem')}	
	ADD COLUMN `order` varchar(255) NOT NULL default '' AFTER `faqitem_id`;
");
$installer->endSetup();