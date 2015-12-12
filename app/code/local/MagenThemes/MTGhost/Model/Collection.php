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
class MagenThemes_MTGhost_Model_Collection extends Mage_Core_Model_Abstract
{
    public function appendType($collection)
    {
        $entityIds = array();
        foreach ($collection->getItems() as $_itemId => $_item) {
            $entityIds[] = $_item->getEntityId();
        }

        if (sizeof($entityIds) == 0) {
            return $this;
        }
        $readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
        $optionTable = $this->getTableName('catalog/product_option');
        foreach ($collection->getItems() as $_item ) {
            $select = $readonce->fetchAll('SELECT type FROM '.$optionTable.' WHERE product_id='.$_item->getEntityId());
            $_item->setOptionsType($select);
        }

        return $this;
    }

    public function getTableName($modelEntity)
    {
        try {
            $table = Mage::getSingleton('core/resource')->getTableName($modelEntity);
        } catch (Exception $e){
            Mage::throwException($e->getMessage());
        }
        return $table;
    }
}
