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
class MagenThemes_MTTheme_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
	
	/**
     * Prepare catalog attribute values to save
	 * From: Mage_Catalog_Model_Resource_Setup
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);

        return $data;
    }
}
