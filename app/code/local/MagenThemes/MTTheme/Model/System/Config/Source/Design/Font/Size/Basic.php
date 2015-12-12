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
class MagenThemes_MTTheme_Model_System_Config_Source_Design_Font_Size_Basic
{
    public function toOptionArray()
    {
		return array(
			array('value' => '12px',	'label' => Mage::helper('adminhtml')->__('12 px')),
			array('value' => '13px',	'label' => Mage::helper('adminhtml')->__('13 px')),
            array('value' => '14px',	'label' => Mage::helper('adminhtml')->__('14 px'))
        );
    }
}