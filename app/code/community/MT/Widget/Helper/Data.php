<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Helper_Data extends Mage_Core_Helper_Abstract{

    const BLOG_POST_ORDER_RANDOM = 'RANDOM';

    public function getProductCollection($type, $value, $params, $limit=10){
        $collection = null;
        if (!is_array($params)) $params = array();

        switch ($type){
            case 'category':
                $collection = $this->_getProductCategoryCollection($value);
                break;
            case 'collection':
                switch ($value){
                    case 'latest':
                        $collection = $this->_getLatestCollection($params);
                        break;
                    case 'bestsell':
                        $collection = $this->_getBestSellerCollection($params);
                        break;
                    case 'new':
                        $collection = $this->_getNewCollection($params);
                        break;
                    case 'discount':
                        $collection = $this->_getDiscountCollection($params);
                        break;
                    case 'id':
                        $collection = $this->_getIdCollection($params);
                        break;
                    case 'related':
                        $collection = $this->_getRelatedCollection();
                        break;
                    case 'upsell':
                        $collection = $this->_getUpSellCollection($limit);
                        break;
                    case 'crosssell':
                        $collection = $this->_getCrossSellCollection();
                        break;
                    case 'mostviewed':
                        $collection = $this->_getMostViewedCollection($params);
                        break;
                    case 'rating':
                        $collection = $this->_getTopRatedCollection($params);
                        break;
                    case 'recentviewed':
                        $collection = $this->_getRecentViewedCollection($params);
                        break;
                    case 'random':
                    default:
                        $collection = $this->_getRandomCollection($params);
                        break;
                }
                break;
        }

        if ($collection){
            $collection->setPage(1, $limit);

            Mage::dispatchEvent('catalog_block_product_list_collection', array(
                'collection' => $collection
            ));
        }

        return $collection;
    }

    /**
     * @param $params array
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _initCollection($params=array()){
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        /* @var $layer Mage_Catalog_Model_Layer */
        $layer = Mage::getSingleton('catalog/layer');
        $layer->prepareProductCollection($collection);
        $collection->addStoreFilter();

        if (isset($params['category_ids'])){
            $this->_addCategoryFilter($collection, $params['category_ids']);
        }

        return $collection;
    }

    /**
     * @param $collection Mage_Catalog_Model_Resource_Product_Collection
     * @param $categoryIds array
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _addCategoryFilter($collection, $categoryIds){
        if (!$categoryIds) return $collection;
        if (!is_array($categoryIds)) $categoryIds = array($categoryIds);

        $fromPart = $collection->getSelect()->getPart('from');

        if (isset($fromPart['cat_index'])){
            $joinConditionStr = $fromPart['cat_index']['joinCondition'];
            $joinConditions = explode(' AND ', $joinConditionStr);
            foreach ($joinConditions as $index => $conditionStr){
                if (strpos($conditionStr, 'cat_index.category_id') !== false){
                    $joinConditions[$index] = sprintf('cat_index.category_id IN (%s)', implode(',', $categoryIds));
                }
            }
            $fromPart['cat_index']['joinCondition'] = implode(' AND ', $joinConditions);
            $collection->getSelect()->setPart('from', $fromPart);
        }

        return $collection;
    }

    protected function _getTopRatedCollection($params){
        $collection = $this->_initCollection($params);

        /* @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $storeId    = Mage::app()->getStore()->getId();

        $select = $connection->select()
            ->from(
                $resource->getTableName('rating/rating_vote_aggregated'),
                array('entity_pk_value', 'rating_total' => 'SUM(percent_approved)')
            )
            ->where('store_id = ?', $storeId)
            ->group('entity_pk_value')
            ->order('rating_total DESC');

        $collection->getSelect()->join(
            array('rating_idx' => $select),
            join(' AND ', array('rating_idx.entity_pk_value = e.entity_id')),
            array('rating_idx.rating_total')
        );

        unset($connection, $select);
        return $collection;
    }

    protected function _getIdCollection($params){
        if (isset($params['category_ids'])) unset($params['category_ids']);
        if (!isset($params['product_ids'])) return null;
        if (!is_array($params['product_ids'])) return null;
        if (!count($params['product_ids'])) return null;

        $collection = $this->_initCollection($params);
        $collection->addIdFilter($params['product_ids']);

        return $collection;
    }

    protected function _getDiscountCollection($params){
        $collection = $this->_initCollection($params);

        /* @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $websiteId  = Mage::app()->getWebsite()->getId();
        /* @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        $customerGroupId = $customerSession->getCustomerGroupId();

        $select = $connection->select()
            ->from($resource->getTableName('catalog/product_index_price'), array('entity_id'))
            ->where('price > final_price')
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId);

        $collection->getSelect()->join(
            array('discount_idx' => $select),
            join(' AND ', array('discount_idx.entity_id = e.entity_id')),
            array()
        );

        unset($connection, $select);
        return $collection;
    }

    protected function _getRandomCollection($params){
        $collection = $this->_initCollection($params);
        $collection->getSelect()->order('RAND()');

        return $collection;
    }

    protected function _getProductCategoryCollection($category) {
        if (!$category) return null;

        $params = array();
        if (!$category instanceof Mage_Catalog_Model_Category){
            $category = Mage::getModel('catalog/category')->load($category, array('entity_id'));
            if ($category->getId()){
                $params = array('category_ids' => $category->getId());
            }else{
                return null;
            }
        }else{
            $params = array('category_ids' => $category);
        }

        $collection = $this->_initCollection($params);

        return $collection;
    }

    protected function _getLatestCollection($params) {
        $collection = $this->_initCollection($params);
        $collection->setOrder('updated_at', 'desc');

        return $collection;
    }

    protected function _getBestSellerCollection($params) {
        $collection = $this->_initCollection($params);

        /* @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('core/resource');
        $orderItems = $resource->getTableName('sales/order_item');
        $orderMain  = $resource->getTableName('sales/order');
        $collection->getSelect()
            ->join(array('item' => $orderItems), 'item.product_id = e.entity_id', array('count' => 'SUM(item.qty_ordered)'))
            ->join(array('order' => $orderMain), 'item.order_id = order.entity_id', array())
            ->where('order.status = ?', 'complete')
            ->group('e.entity_id')
            ->order('count DESC');

        return $collection;
    }

    protected function _getMostViewedCollection($params) {
        $collection = $this->_initCollection($params);
        /* @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $storeId    = Mage::app()->getStore()->getId();

        $select = $connection->select()
            ->from(array('re' => $resource->getTableName('reports/event')), array('object_id', 'views' => 'COUNT(re.event_id)'))
            ->join(array('ret' => $resource->getTableName('reports/event_type')), 're.event_type_id = ret.event_type_id', array())
            ->where('ret.event_name = ?', 'catalog_product_view')
            ->where('re.store_id = ?', $storeId)
            ->group('re.object_id')
            ->order('views desc')
            ->having('views > ?', 0);

        $collection->getSelect()->join(
            array('view_idx' => $select),
            join(' AND ', array('view_idx.object_id = e.entity_id')),
            array()
        );

        unset($connection, $select);
        return $collection;
    }

    protected function _getRecentViewedCollection($params) {

        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();

        $collection = Mage::getModel('reports/product_index_viewed')
            ->getCollection()
            ->addAttributeToSelect($attributes);

        if ($params['customer_id']) {
            $collection->setCustomerId($params['customer_id']);
        }

        $collection->excludeProductIds(Mage::getModel('reports/product_index_viewed')->getExcludeProductIds())
            ->addUrlRewrite()
            ->setCurPage(1);

        /* Price data is added to consider item stock status using price index */
        $collection->addPriceData();

        /* Price data is added to consider item stock status using price index */
        $collection->addIndexFilter();
        $collection->setAddedAtOrder();
        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInSiteFilterToCollection($collection);

        return $collection;
    }

    protected function _getNewCollection($params){
        $today  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = $this->_initCollection($params);
        $collection
            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $today))
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_to_date', 'date' => true, 'from' => $today),
                    array('attribute' => 'news_to_date', 'is'   => new Zend_Db_Expr('null'))
                ),
                '',
                'left'
            )
            ->addAttributeToSort('news_from_date', 'desc');

        return $collection;
    }

    protected function _getUpSellCollection($limit=10){
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');

        if (!$product) return null;

        $collection = $product->getUpSellProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')){
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($collection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $collection
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite();
        }

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $collection->setPage(1, $limit);
        $collection->load();

        /**
         * Updating collection with desired items
         */
        Mage::dispatchEvent('catalog_product_upsell', array(
            'product'       => $product,
            'collection'    => $collection,
            'limit'         => $limit
        ));

        foreach ($collection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $collection;
    }

    protected function _getCrossSellCollection(){
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');

        if (!$product) return null;

        $collection = $product->getCrossSellProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        foreach ($collection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $collection;
    }

    protected function _getRelatedCollection(){
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');

        if (!$product) return null;

        $collection = $product->getRelatedProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')){
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($collection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $collection
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite();
        }

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        foreach ($collection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $collection;
    }

    public function getDate($product){
        $date = $product->getData('special_to_date');
        if ($date){
            $model = Mage::getSingleton('core/date');
            $today = $model->date('Y-m-d H:i:s');
            if ($date > $today){
                return date('F j, Y', strtotime($date));
            }
        }
    }

    public function getImgBlog($content)
    {
        preg_match_all('/(?<!_)url=([\'"])?(.*?)\\1/', $content, $matches);
        if (isset($matches[2][0])) {
            return Mage::getBaseUrl('media') . $matches[2][0];
        } else {
            preg_match_all('/(?<!_)src=([\'"])?(.*?)\\1/', $content, $option);
            return $option[2][0];
        }
    }

    /**
     * Get image after cropping it
     * @param string $content
     * @param int $width
     * @param int $height
     * @param string $ratio
     * @return string
     * @throws
     */
    public function getCropImage($content, $width, $height, $ratio = '')
    {
        preg_match_all('/(?<!_)url=([\'"])?(.*?)\\1/', $content, $matches);
        if (isset($matches[2][0])) {
            $images = explode('/',$matches[2][0]);
            $sourceImage = Mage::getBaseDir('media') . DS . $matches[2][0];
            $image = new Varien_Image($sourceImage);

            list($widthImg, $heightImg, , ) = getimagesize($sourceImage);
            if($ratio == ''){
                $top = 0;
                $left = 0;
                $right = $widthImg - $width;
                $bottom = $heightImg - $height;
            }else{

                list($ratioWidth, $ratioHeight) = explode(':',$ratio);
                if($heightImg * ($ratioWidth / $ratioHeight) > $widthImg) {
                    $cropHeight = $widthImg / ($ratioWidth / $ratioHeight);
                    $cropWidth = $widthImg;
                }
                else {
                    $cropWidth = $heightImg * ($ratioWidth / $ratioHeight);
                    $cropHeight = $heightImg;
                }
                $top = 0;
                $left = 0;
                $right = $widthImg - $cropWidth;
                $bottom = $heightImg - $cropHeight;
            }

            $image->crop($top, $left, $right, $bottom);
            $image->save(Mage::getBaseDir('media'). DS . 'wysiwyg' . DS . 'crop' . DS . $images[1]);

            return Mage::getBaseUrl('media') .'/wysiwyg/crop/' . $images[1];
        } else {
            preg_match_all('/(?<!_)src=([\'"])?(.*?)\\1/', $content, $option);
            return $option[2][0];
        }
    }
}
