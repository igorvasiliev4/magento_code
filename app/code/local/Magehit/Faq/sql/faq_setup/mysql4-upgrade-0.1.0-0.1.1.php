<?php
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('faqitem')};

CREATE TABLE {$this->getTable('faqitem')} (
  `faqitem_id` int(11) unsigned NOT NULL auto_increment,
  `faq_id` int(11) NOT NULL,
  `question` varchar(512) NOT NULL DEFAULT '',
  `answer` text NOT NULL DEFAULT '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`faqitem_id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('faq')}	
	CHANGE `title` `faqname` varchar(255) NOT NULL default '',
	ADD COLUMN `description` varchar(255) NOT NULL default '' AFTER `faqname`,
	DROP COLUMN `filename`,
	DROP COLUMN `content`;
");


$installer->endSetup(); 


//CHANGE `title` `faqname` varchar(255) NOT NULL default '',