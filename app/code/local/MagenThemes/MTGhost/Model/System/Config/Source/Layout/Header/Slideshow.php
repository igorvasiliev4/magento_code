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
class MagenThemes_MTGhost_Model_System_Config_Source_Layout_Header_Slideshow
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'above', 'label' => Mage::helper('adminhtml')->__('Above')),
            array('value' => 'below', 'label' => Mage::helper('adminhtml')->__('Below'))
        );
    }

}