<?php

class Pcarousel_Pcarousel_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    
    public function getAllCategoriesArray($optionList = false)
{
    $categoriesArray = Mage::getModel('catalog/category')
        ->getCollection()
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('entity_id')
        ->addAttributeToSort('path', 'asc')
        ->addFieldToFilter('is_active', array('eq'=>'1'))
        ->load()
        ->toArray();
 
    if (!$optionList) {
        return $categoriesArray;
    }
 
    foreach ($categoriesArray as $categoryId => $category) { 
        if (isset($category['name'])) {
            $categories[] = array(
                'value' => $category['entity_id'],
                'label' => Mage::helper('pcarousel')->__($category['name'])
            );
        }
    }
 
    return $categories;
}

}