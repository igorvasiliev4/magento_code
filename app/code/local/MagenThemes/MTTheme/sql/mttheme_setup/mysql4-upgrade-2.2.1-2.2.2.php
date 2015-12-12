<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package Mato Framework
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       ZooExtension.com
 * @email        magento@cleversoft.co
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'mtmenu_is_category', array(
    'group'				=> 'Main Menu',
    'label'				=> 'Display in menu',
    'note'				=> "Display category in menu. This field is applicable only for top-level categories.",
    'type'				=> 'varchar',
    'input'				=> 'select',
    'source'			=> 'mttheme/system_config_source_category_attribute_source_block_display',
    'visible'			=> true,
    'required'			=> false,
    'backend'			=> '',
    'frontend'			=> '',
    'searchable'		=> false,
    'filterable'		=> false,
    'comparable'		=> false,
    'user_defined'		=> true,
    'visible_on_front'	=> true,
    'wysiwyg_enabled'	=> false,
    'is_html_allowed_on_front'	=> false,
    'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->endSetup();