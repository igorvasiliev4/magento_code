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
class MagenThemes_MTTheme_Model_System_Config_Source_Category_Grid_Columnbelow
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('adminhtml')->__('1')),
            array('value'=>'2', 'label'=>Mage::helper('adminhtml')->__('2')),
            array('value'=>'3', 'label'=>Mage::helper('adminhtml')->__('3'))
        );
    }

}