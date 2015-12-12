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

class MagenThemes_MTTheme_Model_System_Config_Source_Category_Attribute_Source_Block_Types
	extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{	
	/**
     * Get list of available type
     */
	public function getAllOptions()
	{
		if (!$this->_options)
		{
            return array(
                array('value'=>'group', 'label'=>Mage::helper('adminhtml')->__('Group Style')),
                array('value'=>'classic', 'label'=>Mage::helper('adminhtml')->__('Classic Style')),
                array('value'=>'dropdown', 'label'=>Mage::helper('adminhtml')->__('Dropdown Style')),
                array('value'=>'drop_group', 'label'=>Mage::helper('adminhtml')->__('Dropdown/Group Style'))
            );
        }
		return $this->_options;
    }
}
