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
class MagenThemes_MTTheme_Model_System_Config_Source_Product_Layout
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'default', 'label'=>Mage::helper('adminhtml')->__('Default')),
            array('value'=>'horizontal', 'label'=>Mage::helper('adminhtml')->__('Horizontal')),
            array('value'=>'vertical', 'label'=>Mage::helper('adminhtml')->__('Vertical')),
            array('value'=>'custom1', 'label'=>Mage::helper('adminhtml')->__('Custom 1')),
            array('value'=>'custom2', 'label'=>Mage::helper('adminhtml')->__('Custom 2'))
        );
    }

}