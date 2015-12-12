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

$installer->addAttribute('catalog_category', 'mtmenu_is_alignment', array(
    'group'				=> 'Main Menu',
    'label'				=> 'Alignment',
    'note'				=> "Align submenu.",
    'type'				=> 'varchar',
    'input'				=> 'select',
    'source'			=> 'mttheme/system_config_source_category_attribute_source_align_align',
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

$installer->addAttribute('catalog_category', 'mtmenu_is_width', array(
    'group'				=> 'Main Menu',
    'label'				=> 'Width',
    'note'				=> "Submenu width.",
    'type'				=> 'text',
    'input'				=> 'text',
    'visible'			=> true,
    'required'			=> false,
    'backend'			=> '',
    'frontend'			=> '',
    'searchable'		=> false,
    'filterable'		=> false,
    'comparable'		=> false,
    'user_defined'		=> true,
    'visible_on_front'	=> true,
    'wysiwyg_enabled'	=> true,
    'is_html_allowed_on_front'	=> true,
    'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->endSetup();