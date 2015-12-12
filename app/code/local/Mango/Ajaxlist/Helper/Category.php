<?php

class Mango_Ajaxlist_Helper_Category extends Mage_Core_Helper_Abstract {

    /**
     * Template for filter items block
     *
     * @see Mage_Catalog_Block_Layer_Filter
     */
    /* first: get category tree based on current category... */
    function renderCategoryMenuItemHtml($category, $_layer_items, $level = 0, $isLast = false, $isFirst = false, $include_parent = false) {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();

        $children = $category->getChildrenCategories();
        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);
        // prepare list item html classes
        $classes = array();
        $classes[] = 'level' . $level;

        $linkClass = '';

        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
            $linkClass = "category-expand";
        } else {
            $classes[] = 'child';
        }
        if ($this->isItemActive($category->getId())) {
            $classes[] = 'active-filter-option';
        }
        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        if ($include_parent) {

            // assemble list item with attributes
            $htmlLi = '<li';
            foreach ($attributes as $attrName => $attrValue) {
                $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
            }
            $htmlLi .= '>';
            $html[] = $htmlLi;
            $html[] = '<a href="' . $this->getUrl($category->getId()) . '" data-attribute-value="' . $category->getId() . '" class="' . $linkClass . '">';
            $html[] = Mage::helper('core')->escapeHtml($category->getName());

            if ($_layer_items && isset($_layer_items[$category->getId()])) {
                $_count = $_layer_items[$category->getId()]->getCount();
            } else {
                // $_count = $category->getProductCount();
                $_count = Mage::getModel('catalog/layer')->setCurrentCategory($category)->getProductCollection()->getSize();
            }

            //
            //}
            $html[] = '<span class="item-count">(' . (int) $_count . ')</span>';
            $html[] = '</a>';
        }


        // render children
        $htmlChildren = '';
        $j = 0;
        foreach ($activeChildren as $child) {
            
            $htmlChildren .= $this->renderCategoryMenuItemHtml(
                    $child, $_layer_items, ($level + 1), ($j == $activeChildrenCount - 1), ($j == 0), true);
            $j++;
        }
        if (!empty($htmlChildren)) {

            if ($include_parent)
                $html[] = '<ul class="level' . $level . '">';
            $html[] = $htmlChildren;
            if ($include_parent)
                $html[] = '</ul>';
        }
        if ($include_parent)
            $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }

    function getUrl($_value) {
        $_query = Mage::app()->getRequest()->getQuery();

        $_url_param = Mage::app()->getRequest()->getParam('cat');
        
        $_filter = array();
        if (preg_match('/^[0-9,]+$/', $_url_param)) {
            $_filter = explode(',', $_url_param);
        } elseif ((int) $_url_param > 0) {
            $_filter[] = $_url_param;
        }
        //$_value = $this->getValue();
        if (in_array($_value, $_filter)) {
            array_splice($_filter, array_search($_value, $_filter), 1);
        } else {
            $_filter[] = $_value;
        }
        $_filter = array_unique($_filter);
        $_filter = join(",", $_filter);
        $_query['cat'] = $_filter;

        Mage::helper("ajaxlist")->removeAjaxParameters($_query);
        
        $_query[Mage::getBlockSingleton('page/html_pager')->getPageVarName()] = null;

        if (Mage::app()->getRequest()->getControllerName() == "result" && Mage::app()->getRequest()->getModuleName() == "catalogsearch") {
            $_url = Mage::getUrl('*/*', array('_current' => true, '_use_rewrite' => true, '_query' => $_query));
        } else {
            $_url = Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true, '_query' => $_query));
        }
        return $_url; // Mage::helper('core')->escapeUrl($_url);
    }

    function isItemActive($_value) {
        $_url_param = Mage::app()->getRequest()->getParam('cat');

        $_filter = array();
        if (preg_match('/^[0-9,]+$/', $_url_param)) {
            $_filter = explode(',', $_url_param);
        } elseif ((int) $_url_param > 0) {
            $_filter[] = $_url_param;
        }
        if (in_array($_value, $_filter)) {
            return true;
        } else {
            return false;
        }
    }

}
