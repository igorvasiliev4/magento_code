<?php
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('faq')}	
	CHANGE `order` `faq_order` varchar(255) NOT NULL default '';
ALTER TABLE {$this->getTable('faqitem')}	
	CHANGE `order` `faq_order` varchar(255) NOT NULL default '';
	
");


$installer->endSetup(); 


//CHANGE `title` `faqname` varchar(255) NOT NULL default '',