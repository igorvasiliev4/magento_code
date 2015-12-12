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

class MagenThemes_MTTheme_Model_System_Config_Source_Css_Background_Repeat
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'no-repeat',	'label' => Mage::helper('adminhtml')->__('no-repeat')),
            array('value' => 'repeat',		'label' => Mage::helper('adminhtml')->__('repeat')),
            array('value' => 'repeat-x',	'label' => Mage::helper('adminhtml')->__('repeat-x')),
			array('value' => 'repeat-y',	'label' => Mage::helper('adminhtml')->__('repeat-y'))
        );
    }
}