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

class MagenThemes_MTTheme_Model_System_Config_Source_Css_Background_Attachment
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'fixed',	'label' => Mage::helper('adminhtml')->__('fixed')),
            array('value' => 'scroll',	'label' => Mage::helper('adminhtml')->__('scroll'))
        );
    }
}