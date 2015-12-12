<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layer category filter
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Ajaxlist_Model_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category {

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    protected $_appliedCategory = array();

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        if (Mage::app()->getRequest()->getModuleName() == "catalogsearch")
            return $this->applySimple($request, $filterBlock);
        $filter = $request->getParam($this->getRequestVar());
        if (preg_match('/^[0-9,]+$/', $filter)) {
            $filter = array_unique(explode(',', $filter));
        }
        if (!is_array($filter) || !count($filter)) {
            return $this;
        }
        $_product_ids = array();
        $category = $this->getCategory();
        Mage::register('current_category_filter', $category);
        foreach ($filter as $_cat_id) {
            $_cat_filter = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($_cat_id);
            if ($this->_isValidCategory($_cat_filter)) {
                $this->_appliedCategory[] = $_cat_filter;
                //$_new_product_ids = Mage::getResourceModel('catalog/url')->getProductIdsByCategory($_cat_id);
                $children = implode(',', $category->getResource()->getChildren($_cat_filter, true));
                if ($children)
                    $_cat_id.="," . $children;
                $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
                $select = $adapter->select()
                        ->from(Mage::getSingleton('core/resource')->getTableName('catalog/category_product'), array('product_id'))
                        ->where('category_id in ( ' . $_cat_id . ' )')
                        ->order('product_id');
                $_new_product_ids = $adapter->fetchCol($select);
                
                //$_new_product_ids = Mage::getResourceModel('catalog/url')->getProductIdsByCategory($_cat_id);
                
                //print_r($_new_product_ids);
                $_product_ids = array_merge($_product_ids, $_new_product_ids);
            }
        }
        $this->getLayer()->getProductCollection()->addIdFilter($_product_ids);
        foreach ($this->_appliedCategory as $_cat_filter) {
            $this->getLayer()->getState()->addFilter($this->_createItem($_cat_filter->getName(), $_cat_filter->getId()));
        }
        return $this;
    }

    public function applySimple(Zend_Controller_Request_Abstract $request, $filterBlock) {
        $filter = $request->getParam($this->getRequestVar());
        if (preg_match('/^[0-9,]+$/', $filter)) {
            $filter = array_unique(explode(',', $filter));
        }
        if (!is_array($filter) || !count($filter)) {
            return $this;
        }
        $filter = end($filter);
        if (!$filter) {
            return $this;
        }
        $this->_categoryId = $filter;
        Mage::register('current_category_filter', $this->getCategory(), true);
        $this->_appliedCategory[] = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($filter);
        foreach ($this->_appliedCategory as $_cat_filter) {
            if ($this->_isValidCategory($_cat_filter)) {
                $this->getLayer()->getProductCollection()
                        ->addCategoryFilter($_cat_filter);
                $this->getLayer()->getState()->addFilter(
                        $this->_createItem($_cat_filter->getName(), $filter)
                );
            }
        }
        return $this;
    }

    /**
     * Validate category for be using as filter
     *
     * @param   Mage_Catalog_Model_Category $category
     * @return unknown
     */
    protected function _isValidCategory($category) {
        return $category->getId();
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName() {
        return Mage::helper('catalog')->__('Category');
    }

    /**
     * Get selected category object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory() {
        if (Mage::registry("current_category"))
            return Mage::registry("current_category");
        else {
            if (!is_null($this->_categoryId)) {
                $category = Mage::getModel('catalog/category')
                        ->load($this->_categoryId);
                if ($category->getId()) {
                    return $category;
                }
            }
            return $this->getLayer()->getCurrentCategory();
        }
    }

    /**
     * Get filter value for reset current filter state
     * if(Mage::registry("current_category")) return Mage::registry("current_category");
      else{
      if (!is_null($this->_categoryId)) {
      $category = Mage::getModel('catalog/category')
      ->load($this->_categoryId);
      if ($category->getId()) {
      return $category;
      }
      }
      return $this->getLayer()->getCurrentCategory();
      }
     * @return mixed
     */
    public function getResetValue() {
        if ($this->_appliedCategory && is_array($this->_appliedCategory) && count($this->_appliedCategory)) {
            /**
             * Revert path ids
             */
            foreach ($this->_appliedCategory as $_app_cat) {
                $pathIds = array_reverse($_app_cat->getPathIds());
                $curCategoryId = $this->getLayer()->getCurrentCategory()->getId();
                if (isset($pathIds[1]) && $pathIds[1] != $curCategoryId) {
                    return $pathIds[1];
                }
            }
        }
        return null;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData() {
        if ($_module = Mage::app()->getRequest()->getModuleName() == "catalogsearch")
            return $this->_getItemsDataSearch();
        $key = $this->getLayer()->getStateKey() . '_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $category = $this->getCategory();
            /** @var $categoty Mage_Catalog_Model_Categeory */
            $categories = $this->_getChildrenCategories($category);
             //$this->getLayer()->getProductCollection()
                  // ->addCountToCategories($categories);
            $data = array();
            foreach ($categories as $category) {
                if ($category->getIsActive()) {
                    $_count = Mage::getModel('catalog/layer')->setCurrentCategory($category)->getProductCollection()->getSize();
                    //$_count = $category->getProductCount() ;
                    //Mage::getModel('catalog/layer')->setCurrentCategory($category)->getProductCollection()->getSize();
                    if (!Mage::getStoreConfig("ajaxlist/ajaxlist/show_no_count") && !$_count) {
                        continue;
                    }
                    $data[] = array(
                        'label' => Mage::helper('core')->htmlEscape($category->getName()),
                        'value' => $category->getId(),
                        'count' => $_count,
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsDataSearch() {
        $key = $this->getLayer()->getStateKey() . '_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        $_module = Mage::app()->getRequest()->getModuleName();
        if (!$_module)
            return $data;
        if ($data === null) {

            $category = $this->getCategory();
            /** @var $categoty Mage_Catalog_Model_Categeory */
            //$categories = $category->getChildrenCategories();
            $categories = $this->_getChildrenCategories($category);
            if (!count($categories)) {
                $category = Mage::getModel('catalog/category')->load($category->getParentId());
                $categories = $category->getChildrenCategories();
            }
            $this->getLayer()->getProductCollection()
                    ->addCountToCategories($categories);
            $_pids = $this->getLayer()->getProductCollection()->getAllIds();
            $data = array();
            foreach ($categories as $category) {
                if ($category->getIsActive()) {
                   // $_count = $category->getProductCollection()
                       //     ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            //->addIdFilter( $pids );
                        //    ->addAttributeToFilter('entity_id', array('in' => $_pids));
                    
                   // $_count = Mage::getModel('catalogsearch/layer')->setCurrentCategory($category)->getProductCollection()->getSize();
                    
                    //Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($_count);
                    //$_count = $_count->getSize();
                    
                    $_count = $category->getProductCount();
                    
                    if (!Mage::getStoreConfig("ajaxlist/ajaxlist/show_no_count") && !$_count) {
                        continue;
                    }
                    $data[] = array(
                        'label' => Mage::helper('core')->htmlEscape($category->getName()),
                        'value' => $category->getId(),
                        'count' => $_count,
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    protected function _getChildrenCategories($category) {
        //getCategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
        return Mage::getModel("catalog/category")->getCategories($category->getId(), 10000, false, true, true);
        
    }
}
