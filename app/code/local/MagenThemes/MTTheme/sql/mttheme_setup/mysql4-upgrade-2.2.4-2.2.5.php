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

$installer->addAttribute('catalog_category', 'mtmenu_is_icon', array(
    'group'				=> 'Main Menu',
    'label'				=> 'Icon',
    'note'				=> "Add icon to level top menu.",
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
    'wysiwyg_enabled'	=> false,
    'is_html_allowed_on_front'	=> false,
    'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->endSetup();