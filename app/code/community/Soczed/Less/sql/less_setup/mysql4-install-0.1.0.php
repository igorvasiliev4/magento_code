<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Soczed
 * @package    Soczed_Less
 * @copyright  Copyright (c) 2012 Soczed <magento@soczed.com> (Benoît Leulliette <benoit@soczed.com>)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('less/file')}` (
    `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `path` text character set utf8 NOT NULL,
    `cache` text character set utf8 default NULL,
    `use_global_variables` tinyint(1) unsigned NOT NULL default 1,
    `force_global_variables` tinyint(1) unsigned NOT NULL default 0,
    `custom_variables` text character set utf8 default NULL,
    `force_rebuild` tinyint(1) unsigned NOT NULL default 0,
    PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();