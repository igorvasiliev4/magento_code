<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('faq_store')};
CREATE TABLE {$this->getTable('faq_store')} (
  `faq_id` int(11) unsigned NOT NULL,
  `store_id` int(6) NOT NULL  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('faqitem_store')};
CREATE TABLE {$this->getTable('faqitem_store')} (
  `faqitem_id` int(11) unsigned NOT NULL,
  `store_id` int(6) NOT NULL  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();