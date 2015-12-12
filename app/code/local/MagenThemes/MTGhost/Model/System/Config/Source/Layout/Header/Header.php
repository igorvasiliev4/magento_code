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
class MagenThemes_MTGhost_Model_System_Config_Source_Layout_Header_Header
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'layout1', 'label' => Mage::helper('adminhtml')->__('Layout 1')),
            array('value' => 'layout2', 'label' => Mage::helper('adminhtml')->__('Layout 2')),
            array('value' => 'layout3', 'label' => Mage::helper('adminhtml')->__('Layout 3')),
            array('value' => 'layout4', 'label' => Mage::helper('adminhtml')->__('Layout 4')),
            array('value' => 'layout5', 'label' => Mage::helper('adminhtml')->__('Layout 5'))
        );
    }

}