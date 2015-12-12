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
class MagenThemes_MTTheme_Model_System_Config_Source_Layout_Element_Displayonhover
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('adminhtml')->__('Don\'t Display')),
            array('value'=>'1', 'label'=>Mage::helper('adminhtml')->__('Display On Hover')),
            array('value'=>'2', 'label'=>Mage::helper('adminhtml')->__('Display'))
        );
    }

}