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
class MagenThemes_MTTheme_Model_System_Config_Source_Category_Altimagecolumn
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'label', 'label'=>Mage::helper('adminhtml')->__('Label')),
            array('value'=>'position', 'label'=>Mage::helper('adminhtml')->__('Sort Order'))
        );
    }

}