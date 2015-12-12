<?php
class Mango_Ajaxlist_Model_Resource_Eav_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    protected  $_category_filters = array();
    /**
     * Specify category filter for product collection
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addCategoryFilters( $_categories )
    {
        if(is_array($_categories)){
            foreach($_categories as $_category){
                $this->_category_filters[] = $_category->getId();
            }
        }
        /*if ($category->getIsAnchor()) {
            unset($this->_productLimitationFilters['category_is_anchor']);
        }
        else {
            $this->_productLimitationFilters['category_is_anchor'] = 1;
        }*/
        ($this->getStoreId() == 0)? $this->_applyZeroStoreProductLimitations() : $this->_applyProductLimitations();
        return $this;
    }
    
    protected function _applyProductLimitations()
    {
        $this->_prepareProductLimitationFilters();
        $this->_productLimitationJoinWebsite();
        $this->_productLimitationJoinPrice();
        $filters = $this->_productLimitationFilters;
        $_multiple_categories_filter = Mage::app()->getRequest()->getParam("cat");
        if($_multiple_categories_filter && !isset($this->_category_filters)){
            return $this;
        }
        if (!isset($filters['category_id']) &&  !isset($this->_category_filters)   && !isset($filters['visibility'])) {
            return $this;
        }
        //if(!isset($this->_category_filters)){
        $conditions = array(
            'cat_index.product_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_index.store_id=?', $filters['store_id'])
        );
       // }
        if (isset($filters['visibility']) && !isset($filters['store_table'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.visibility IN(?)', $filters['visibility']);
        }
        
        if($this->_category_filters && count($this->_category_filters) ){
            $_data = join("," , $this->_category_filters);
            $conditions[] = $this->getConnection()->quoteInto('cat_index.category_id IN(' . $_data .  ')');
        }elseif(     $filters['category_id']           ){
            $conditions[] = $this->getConnection()->quoteInto('cat_index.category_id=?', $filters['category_id']);
        }
        if (isset($filters['category_is_anchor'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.is_parent=?', $filters['category_is_anchor']);
        }
        $joinCond = join(' AND ', $conditions);
        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_index'])) {
         //  if(!isset($this->_category_filters)){  //removed sort order by position when selected more than one category
            $fromPart['cat_index']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        //   }
        }
        else {
            $this->getSelect()->join(
                array('cat_index' => $this->getTable('catalog/category_product_index')),
                $joinCond,
                array('cat_index_position' => 'position')
            );
        } 
        //echo "<br/><br/>" . $this->getSelect() . "<br/><br/>";
        //exit;
        $this->_productLimitationJoinStore();
        Mage::dispatchEvent('catalog_product_collection_apply_limitations_after', array(
            'collection'    => $this
        ));
        return $this;
    }
   
}
