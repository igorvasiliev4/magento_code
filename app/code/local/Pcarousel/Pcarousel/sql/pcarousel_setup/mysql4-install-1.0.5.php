<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('pcarousel')};
CREATE TABLE {$this->getTable('pcarousel')} (
  `pcarousel_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `categories` varchar(255) NOT NULL default '',
  `numberofproduct` varchar(255) NOT NULL default '',
  `paginations` smallint(6) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`pcarousel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 