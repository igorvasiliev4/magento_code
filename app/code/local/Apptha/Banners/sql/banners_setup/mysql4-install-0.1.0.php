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
* @package     Apptha_Banners
* @version     0.1.0
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
*
*/

	$this->startSetup();
	
	$this->run("
	
		DROP TABLE IF EXISTS {$this->getTable('apptha_slides_details')};		
		CREATE TABLE IF NOT EXISTS {$this->getTable('apptha_slides_details')} (
			`id` int(11) unsigned NOT NULL auto_increment,
			`store_id` int(11) unsigned NOT NULL default 0,
			`title` varchar(255) NOT NULL default '',
			`slide_code` varchar(32) NOT NULL default '',
			`is_enabled` tinyint(1) unsigned NOT NULL default 1,
			`randomise_banners` int(1) unsigned NOT NULL DEFAULT '0',
			`autoplay` int(1) unsigned DEFAULT NULL,
			`items_count` int(5) unsigned DEFAULT NULL,
			`slide_speed` int(11) unsigned DEFAULT NULL,
			`pagination` int(1) unsigned DEFAULT NULL,
			`slider_effect` varchar(32) NOT NULL DEFAULT '',
			PRIMARY KEY (`id`),
			UNIQUE KEY `slide_code` (`slide_code`,`store_id`)	
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		INSERT INTO {$this->getTable('apptha_slides_details')} (store_id, title, slide_code, is_enabled) VALUES (0, 'Homepage', 'home', 1);

		DROP TABLE IF EXISTS {$this->getTable('apptha_banners_details')};
		CREATE TABLE IF NOT EXISTS {$this->getTable('apptha_banners_details')} (
			`id` int(11) unsigned NOT NULL auto_increment,
			`title` varchar(255) NOT NULL default '',
			`url` varchar(255) NOT NULL default '',
			`image` varchar(255) NOT NULL default '',
			`alt_text` varchar(255) NOT NULL default '',
			`html` text NOT NULL,
			`is_enabled` tinyint(1) unsigned NOT NULL default 1,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		DROP TABLE IF EXISTS {$this->getTable('apptha_slide_banner')};		
		CREATE TABLE IF NOT EXISTS {$this->getTable('apptha_slide_banner')} (
			`id` int(11) unsigned NOT NULL auto_increment,
			`slide_id` int(11) unsigned NOT NULL ,
			`banner_id` int(11) unsigned NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	");
	
	$this->endSetup();
