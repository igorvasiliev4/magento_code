<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Block_Widget extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface {
    const CACHE_GROUP = 'mt_widget';
    protected static $_helper;
    protected $_productCollection;

    protected function _construct(){
        parent::_construct();
        if (!self::$_helper && Mage::helper('core')->isModuleEnabled('AW_Blog')) {
            self::$_helper = Mage::helper('blog');
        }
        $this->setData('cache_tags', array(self::CACHE_GROUP, Mage_Core_Block_Template::CACHE_GROUP));
    }

    public function getCacheLifetime(){
        return $this->getData('cache') ? (int)$this->getData('cache') : null;
    }

    public function getCacheKeyInfo(){
        return array(
            self::CACHE_GROUP,
            Mage::app()->getStore()->getId(),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            $this->getData('widget_type'),
            $this->getData('widget_tab'),
            $this->getData('collections'),
            $this->getData('category_ids'),
            $this->getData('product_ids'),
            $this->getData('attribute'),
            $this->getData('attribute_options'),
            $this->getData('attribute_link'),
            $this->getData('block_ids'),
            $this->getData('mode'),
            $this->getData('limit'),
            $this->getData('scroll'),
            $this->getData('row'),
            $this->getData('column'),
            $this->getData('animate'),
            $this->getData('background')
        );
    }

    protected function _beforeToHtml(){
        if ($this->getTemplate() == 'mt/widget/default.phtml'){
            switch ($this->getData('widget_type')){
                case 'product':
                    switch ($this->getData('mode')){
                        case 'related':
                            $this->setTemplate('mt/widget/related.phtml');
                            break;
                        default:
                            $this->setTemplate('mt/widget/product.phtml');
                            break;
                    }
                    switch ($this->getData('widget_tab')){
                        case 'categories':
                        case 'collections':
                            $this->setTemplate('mt/widget/tab.phtml');
                            break;
                    }
                    break;
                case 'attribute':
                    $this->setTemplate('mt/widget/attribute.phtml');
                    break;
                case 'block':
                    $this->setTemplate('mt/widget/block.phtml');
                    break;
                case 'category':
                    $this->setTemplate('mt/widget/category.phtml');
                    break;
                case 'blog':
                    $this->setTemplate('mt/widget/blog.phtml');
                    break;
            }
        }
        return parent::_beforeToHtml();
    }

    public function getCategories(){
        if (!$this->getData('category_ids')) return array();

        $categories = array();
        foreach (explode(',', $this->getData('category_ids')) as $categoryId){
            /* @var $category Mage_Catalog_Model_Category */
            $category = Mage::getModel('catalog/category')
                ->setStore(Mage::app()->getStore())
                ->load($categoryId, array('name', 'image', 'thumbnail'));
            if ($category->getId()){
                $categories[] = array(
                    'url'   => $category->getUrl(),
                    'label' => $category->getName(),
                    'image' => $category->getThumbnail() ? Mage::getBaseUrl('media'). 'catalog/category/' . $category->getThumbnail() : '',
                    'count' => $category->getProductCount()
                );
            }
        }

        return $categories;
    }

    public function getTabs(){
        $tabs = array();
        $type = $this->getData('widget_tab');
        $labels = explode('||', $this->getData('labels'));

        switch ($type){
            case 'categories':
                $categoryIds = explode(',', $this->getData('category_ids'));
                foreach ($categoryIds as $index => $categoryId){
                    /* @var $categoryModel Mage_Catalog_Model_Category */
                    $categoryModel = Mage::getModel('catalog/category')->load($categoryId, array('name'));
                    if ($categoryModel->getId()){
                        $tabs[] = array(
                            'type'  => 'category',
                            'id'    => 'category-' . $categoryModel->getId(),
                            'label' => isset($labels[$index]) && $labels[$index] ? $labels[$index] : $categoryModel->getName(),
                            'value' => $categoryModel->getId()
                        );
                    }
                }
                break;
            case 'collections':
                $collectionNames = $this->getData('collections');
                if (!is_array($collectionNames)) $collectionNames = explode(',', $this->getData('collections'));
                foreach ($collectionNames as $index => $collectionName){
                    $tabs[] = array(
                        'type'  => 'collection',
                        'id'    => 'collection-' . $collectionName,
                        'label' => isset($labels[$index]) && $labels[$index] ? $labels[$index] : $collectionName,
                        'value' => $collectionName
                    );
                }
                break;
        }

        return $tabs;
    }

    public function checkCollectionSize($type, $value){
        $collection = $this->_getProductCollection($type, $value);
        return $collection->count();
    }

    public function renderCollection($type, $value, $template='mt/widget/collection.phtml'){
        /* @var $block MT_Widget_Block_Widget_Collection */
        $block = $this->getLayout()->createBlock('mtwidget/widget_collection');

        $block->setData(array(
            'row'           => $this->getConfig('row'),
            'column'        => $this->getConfig('column'),
            'carousel'      => $this->getConfig('scroll'),
            'collection'    => $this->_getProductCollection($type, $value)
        ));
        $block->setTemplate($template);

        return $block->toHtml();
    }

    protected function _getProductCollection($type, $value){
        if (!$this->_productCollection){
            /* @var $helper MT_Widget_Helper_Data */
            $helper = Mage::helper('mtwidget');
            $params = array();

            if ($this->getData('category_ids')){
                $params['category_ids'] = explode(',', $this->getData('category_ids'));
            }
            if ($this->getData('product_ids')){
                $params['product_ids'] = explode(',', $this->getData('product_ids'));
            }
            if ($this->getCustomerId()) {
                $params['customer_id'] = $this->getCustomerId();
            }
            $this->_productCollection = $helper->getProductCollection($type, $value, $params, $this->getLimit());
        }

        return $this->_productCollection;
    }

    public function getLimit(){
        return is_numeric($this->getData('limit')) ? $this->getData('limit') : 10;
    }

    public function getBlocks(){
        $blocks     = array();
        $layout     = $this->getLayout();
        $storeId    = Mage::app()->getStore()->getId();

        $classes    = array();
        $order      = array();

        foreach (array('lg', 'md', 'sm', 'xs') as $l){
            foreach (explode('|', $this->getData('block_' . $l)) as $block){
                list($blockId, $column, $cls) = explode(',', $block);

                if (!isset($classes[$blockId])){
                    $classes[$blockId] = "col-{$l}-{$column} ";
                }else{
                    $classes[$blockId] .= "col-{$l}-{$column} ";
                }
                $classes[$blockId] .= "{$cls} ";

                if (!in_array($blockId, $order)){
                    $order[] = $blockId;
                }
            }
        }

        foreach ($order as $blockId){
            /* @var $collection Mage_Cms_Model_Resource_Block_Collection */
            $collection = Mage::getResourceModel('cms/block_collection')
                ->addFieldToFilter('identifier', array('eq' => $blockId));

            if ($collection->count()){
                /* @var $block Mage_Cms_Model_Block */
                $block = $collection->getFirstItem();
                $block->load($block->getId());
                $storeIds = $block->getStoreId();
                if ($block->getIsActive() && (in_array($storeId, $storeIds) || in_array(0, $storeIds))){
                    $blocks[] = array(
                        'class'     => isset($classes[$blockId]) ? $classes[$blockId] : '',
                        'content'   => $layout->createBlock('cms/block')->setStoreId()->setBlockId($blockId)->toHtml()
                    );
                }
            }
        }

        return $blocks;
    }

    public function getBlog()
    {
        if(Mage::helper('core')->isModuleEnabled('AW_Blog')) {
            $catids = (array)$this->getData('blog_cat_ids');
            $collection = Mage::getModel('blog/blog')->getCollection()
                ->addPresentFilter()
                ->addEnableFilter(AW_Blog_Model_Status::STATUS_ENABLED)
                ->addStoreFilter()
                ->joinComments()
                ->addCatsFilter(implode(',', $catids));

            //Set limit article to display
            $collection->setPageSize((int)$this->getData('count'));

            //Set order
            $sortDirection = $this->getData('order');
            if($sortDirection == MT_Widget_Helper_Data::BLOG_POST_ORDER_RANDOM){
                $collection->getSelect()->orderRand(new Zend_Db_Expr('RAND()'));
            } else {
                $collection->setOrder('created_time', $sortDirection);
            }

            foreach ($collection as $item) {
                $item->setPostContent($this->truncate(strip_tags($item->getShortContent()), $length = 100, $etc = ' ...'));
                $item->setAddress($this->getBlogUrl($item->getIdentifier()));
            }
            return $collection;
        }
    }

    public function truncate($string, $chars = 50, $terminator = ' ...')
    {
        $cutPos = $chars - mb_strlen($terminator);
        $boundaryPos = mb_strrpos(mb_substr($string, 0, mb_strpos($string, ' ', $cutPos)), ' ');
        return mb_substr($string, 0, $boundaryPos === false ? $cutPos : $boundaryPos) . $terminator;
    }

    public function getBlogUrl($route = null, $params = array())
    {
        $blogRoute = self::$_helper->getRoute();
        if (is_array($route)) {
            foreach ($route as $item) {
                $item = urlencode($item);
                $blogRoute .= "/{$item}";
            }
        } else {
            $blogRoute .= "/{$route}";
        }

        foreach ($params as $key => $value) {
            $value = urlencode($value);
            $blogRoute .= "{$key}/{$value}/";
        }

        return $this->getUrl($blogRoute);
    }

    public function getAttibuteOptions(){
        $showOptions = explode(',', $this->getData('attribute_options'));
        list($attributeId, $attributeCode) = explode(',' , $this->getData('attribute'));

        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attributeId)
            ->setStoreFilter()
            ->load();

        $options = array();
        foreach ($optionCollection as $option){
            if (in_array($option->getId(), $showOptions)){
                if ($this->getData('attribute_link')){
                    $options[] = array(
                        'id'    => $option->getId(),
                        'label' => $option->getValue(),
                        'image' => $option->getImage() ?
                                (
                                    strpos($option->getImage(), 'http') === 0 ?
                                    $option->getImage() :
                                    Mage::getBaseUrl('media') . $option->getImage()
                                ):
                                null,
                        'link'  => $this->getUrl('catalogsearch/result/index', array('q' => $option->getValue()))
                    );
                }else{
                    $options[] = array(
                        'id'    => $option->getId(),
                        'label' => $option->getValue(),
                        'image' => $option->getImage() ?
                                (
                                    strpos($option->getImage(), 'http') === 0 ?
                                    $option->getImage() :
                                    Mage::getBaseUrl('media') . $option->getImage()
                                ):
                                null
                    );
                }
            }
        }

        return $options;
    }

    protected function _getKenburnsImages(){
        $prefix = Mage::getBaseUrl('media');
        $images = $this->getData('kenburns_images');
        $out = array();

        if (!is_array($images)){
            $images = explode(',', $images);
        }

        foreach ($images as $image){
            if ($image){
                $out[] = strpos($image, 'http') !== false ? $image : $prefix . $image;
            }
        }

        if (count($out) == 1){
            $out[] = $out[0];
        }

        return $out;
    }

    public function getConfig($name, $param=null){
        /* @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');

        switch ($name){
            case 'countdown':
                return $helper->jsonEncode(array(
                    'enable'            => (bool) $this->getData('countdown'),
                    'yearText'          => Mage::helper('mtwidget')->__('years'),
                    'monthText'         => Mage::helper('mtwidget')->__('months'),
                    'weekText'          => Mage::helper('mtwidget')->__('weeks'),
                    'dayText'           => Mage::helper('mtwidget')->__('days'),
                    'hourText'          => Mage::helper('mtwidget')->__('hours'),
                    'minText'           => Mage::helper('mtwidget')->__('mins'),
                    'secText'           => Mage::helper('mtwidget')->__('secs'),
                    'yearSingularText'  => Mage::helper('mtwidget')->__('year'),
                    'monthSingularText' => Mage::helper('mtwidget')->__('month'),
                    'weekSingularText'  => Mage::helper('mtwidget')->__('week'),
                    'daySingularText'   => Mage::helper('mtwidget')->__('day'),
                    'hourSingularText'  => Mage::helper('mtwidget')->__('hour'),
                    'minSingularText'   => Mage::helper('mtwidget')->__('min'),
                    'secSingularText'   => Mage::helper('mtwidget')->__('sec'),
                    'engineSrc'         => Mage::getBaseUrl('js') . 'mt/extensions/jquery/plugins/jquery.jcountdown.min.js'
                ));
                break;
            case 'kenburns':
                return $helper->jsonEncode(array(
                    'enable'    => $this->getData('background') == 'kenburns',
                    'images'    => $this->_getKenburnsImages(),
                    'overlay'   => $this->getData('background_overlay'),
                    'engineSrc' => Mage::getBaseUrl('js') . 'mt/extensions/jquery/plugins/kenburns.js'
                ));
                break;
            case 'parallax':
                return $helper->jsonEncode(array(
                    'enable'    => $this->getData('background') == 'parallax',
                    'type'      => $this->getData('parallax_type'),
                    'overlay'   => $this->getData('background_overlay'),
                    'video'     => array(
                        'src'       => $this->getData('parallax_video_src'),
                        'volume'    => (bool) $this->getData('parallax_video_volume'),
                    ),
                    'image'     => array(
                        'src'       => $this->getData('parallax_image_src') ?
                                        (
                                            strpos($this->getData('parallax_image_src'), 'http') === 0 ?
                                            $this->getData('parallax_image_src') :
                                            Mage::getBaseUrl('media') . $this->getData('parallax_image_src')
                                        ):
                                        null,
                        'fit'       => $this->getData('parallax_image_fit'),
                        'repeat'    => $this->getData('parallax_image_repeat')
                    ),
                    'file'      => array(
                        'poster'    => $this->getData('parallax_video_poster') ?
                                        (
                                            strpos($this->getData('parallax_video_poster'), 'http') === 0 ?
                                            $this->getData('parallax_video_poster') :
                                            Mage::getBaseUrl('media') . $this->getData('parallax_video_poster')
                                        ):
                                        null,
                        'mp4'       => $this->getData('parallax_video_mp4') ?
                                        (
                                            strpos($this->getData('parallax_video_mp4'), 'http') === 0 ?
                                            $this->getData('parallax_video_mp4') :
                                            Mage::getBaseUrl('media') . $this->getData('parallax_video_mp4')
                                        ):
                                        null,
                        'webm'      => $this->getData('parallax_video_webm') ?
                                        (
                                            strpos($this->getData('parallax_video_webm'), 'http') === 0 ?
                                            $this->getData('parallax_video_webm') :
                                            Mage::getBaseUrl('media') . $this->getData('parallax_video_webm')
                                        ):
                                        null,
                        'volume'    => (bool) $this->getData('parallax_video_volume')
                    )
                ));
                break;
            case 'carousel':
                return $helper->jsonEncode(array(
                    'enable'        => (bool) $this->getData('scroll'),
                    'pagination'    => (bool) $this->getData('paging'),
                    'autoPlay'      => is_numeric($this->getData('autoplay')) ? (int) $this->getData('autoplay') : false,
                    'items'         => is_numeric($this->getData('column')) ? (int) $this->getData('column') : 4,
                    'singleItem'    => $this->getData('column') == 1,
                    'lazyLoad'      => true,
                    'lazyEffect'    => false,
                    'addClassActive'=> true,
                    'navigation'    => (bool) $this->getData('navigation'),
                    'navigationText'=> array($this->getData('navigation_prev'), $this->getData('navigation_next')),
                    'engineSrc'     => Mage::getBaseUrl('js') . 'mt/extensions/jquery/plugins/owl-carousel/owl.carousel.js'
                ));
                break;
            case 'animation':
                return $helper->jsonEncode(array(
                    'enable'        => (bool) $this->getData('animate'),
                    'animationName' => $this->getData('animation'),
                    'animationDelay'=> is_numeric($this->getData('animation_delay')) ? (int) $this->getData('animation_delay') : 300,
                    'itemSelector'  => $this->getData('animate_item_selector'),
                    'engineSrc'     => Mage::getBaseUrl('js') . 'mt/extensions/wow/wow.js'
                ));
                break;
            case 'widget_title':
                return $this->escapeHtml($this->getData('widget_title'));
                break;
            case 'id':
                return $helper->uniqHash(is_string($param) ? $param : 'widget-');
                break;
            case 'row':
                return is_numeric($this->getData('row')) ? (int) $this->getData('row') : 1;
                break;
            case 'column':
                return is_numeric($this->getData('column')) ? (int) $this->getData('column') : 4;
                break;
            case 'limit':
                return is_numeric($this->getData('limit')) ? (int) $this->getData('limit') : 1;
                break;
            case 'classes':
                return $this->getData('classes') . ($this->getData('animate') ? ' ' . $this->getData('animation') : '');
                break;
            default:
                return $this->getData($name);
        }
    }
}
