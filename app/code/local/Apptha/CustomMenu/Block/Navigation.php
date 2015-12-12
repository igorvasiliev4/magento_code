<?php

/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.2.3
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */

/**
 * Header Menu Responsive for mobile and tablet
 */
class Apptha_CustomMenu_Block_Navigation extends Mage_Catalog_Block_Navigation {
    
    /**
     * Define constant variables
     */
    const CUSTOM_BLOCK_TEMPLATE = "custom_menu_%d";
    private $_productsCount = null;
    private $_topMenu = array ();
    private $_popupMenu = array ();
    /**
     * Function to display mobile responsive menu
     *
     * Passed the category to get the header menu hierarchy
     *
     * @param int $category
     *            Set the hierarchy level
     * @param int $level
     *            Check whether the sub menu is last or not
     * @param bool $last
     *            This Function will return Header menu display hierarchy for web
     * @return string
     */
    public function drawCustomMenuMobileItem($category, $level = 0) {
        if (! $category->getIsActive ()) {
            return '';
        }
        $html = array ();
        $id = $category->getId ();
        /**
         * Sub Categories
         */
        $activeChildren = $this->_getActiveChildren ( $category, $level );
        /**
         * class for active category
         */
        $active = '';
        if ($this->isCategoryActive ( $category )) {
            $active = ' act';
        }
        
        $hasSubMenu = count ( $activeChildren );
        $catUrl = $this->getCategoryUrl ( $category );
        $html [] = '<div id="menu-mobile-' . $id . '" class="menu-mobile level0' . $active . '">';
        $html [] = '<div class="parentMenu">';
        /**
         * Top Menu Items
         */
        $html [] = '<a href="' . $catUrl . '">';
        $name = $this->escapeHtml ( $category->getName () );
        if (Mage::getStoreConfig ( 'custom_menu/general/non_breaking_space' )) {
            $name = str_replace ( ' ', '&nbsp;', $name );
        }
        $html [] = '<span>' . $name . '</span>';
        $html [] = '</a>';
        /**
         * check condition submenu is true
         */
        if ($hasSubMenu) {
            $html [] = '<span class="button" rel="submenu-mobile-' . $id . '" onclick="SubMenuToggle(this, \'menu-mobile-' . $id . '\', \'submenu-mobile-' . $id . '\');">&nbsp;</span>';
        }
        $html [] = '</div>';
        /**
         * Add Popup block (hidden)
         */
        if ($hasSubMenu) {
            /**
             * draw Sub Categories
             */
            $html [] = '<div id="submenu-mobile-' . $id . '" rel="level' . $level . '" class="custom-menu-submenu" style="display: none;">';
            $html [] = $this->drawMobileMenuItem ( $activeChildren );
            $html [] = '<div class="clearBoth"></div>';
            $html [] = '</div>';
        }
        $html [] = '</div>';
        return implode ( "\n", $html );
    }
    
    /**
     * Get the Top menu array
     *
     * This Function will return the top menu's
     *
     * @return string
     */
    public function getTopMenuArray() {
        return $this->_topMenu;
    }
    
    /**
     * Get the pop up menu array
     *
     * This Function will return the pop up menu's
     *
     * @return string
     */
    public function getPopupMenuArray() {
        return $this->_popupMenu;
    }
    
    /**
     * Function to display custom menu(home) mobile responsive menu
     *
     * Passed the category to get the header menu hierarchy
     *
     * @param int $category
     *            Set the hierarchy level
     * @param int $level
     *            Check whether the sub menu is last or not
     * @param bool $last
     *            This Function will return Header menu display hierarchy for web
     * @return string
     */
    public function drawCustomMenuItem($category, $level = 0) {
        /**
         * check condition category is active is equal to false
         */
        if (! $category->getIsActive ()) {
            return;
        }
        $htmlTop = array ();
        $id = $category->getId ();
        $imageSrc = Mage::getModel ( 'catalog/category' )->load ( $id )->getThumbnail ();
        /**
         * Static Block
         */
        $blockId = sprintf ( static::CUSTOM_BLOCK_TEMPLATE, $id );
        /**
         * Mage::log($blockId);
         */
        $collection = Mage::getModel ( 'cms/block' )->getCollection ()->addFieldToFilter ( 'identifier', array (
                array (
                        'like' => $blockId . '_w%' 
                ),
                array (
                        'eq' => $blockId 
                ) 
        ) )->addFieldToFilter ( 'is_active', 1 );
        $blockId = $collection->getFirstItem ()->getIdentifier ();
        /**
         * Mage::log($blockId);
         */
        $blockHtml = Mage::app ()->getLayout ()->createBlock ( 'cms/block' )->setBlockId ( $blockId )->toHtml ();
        /**
         * Sub Categories
         */
        $activeChildren = $this->_getActiveChildren ( $category, $level );
        
        /**
         * check condition category active is true
         */
        $active = $this->checkActiveCateogry ( $category );
        
        /**
         * Popup functions for show
         */
        $drawPopup = $this->getDrawPopup ( $blockHtml, $activeChildren );
        
        /**
         * check condition draw popup is equal to true
         */
        if ($drawPopup) {
            $htmlTop [] = '<div id="menu' . $id . '" class="menu' . $active . '" onmouseover="ShowMenuPopup(this, event, \'popup' . $id . '\');" onmouseout="HideMenuPopup(this, event, \'popup' . $id . '\', \'menu' . $id . '\')">';
        } else {
            $htmlTop [] = '<div id="menu' . $id . '" class="menu' . $active . '">';
        }
        $htmlTop [] = '<div class="parentMenu">';
        /**
         * check condition level is equal to 0 and draw popup is equal to true
         */
        if ($level == 0 && $drawPopup) {
            $htmlTop [] = '<a href="javascript:void(0);" rel="' . $this->getCategoryUrl ( $category ) . '">';
        } else {
            $htmlTop [] = '<a href="' . $this->getCategoryUrl ( $category ) . '">';
        }
        $name = $this->escapeHtml ( $category->getName () );
        if (Mage::getStoreConfig ( 'custom_menu/general/non_breaking_space' )) {
            $name = str_replace ( ' ', '&nbsp;', $name );
        }
        $htmlTop [] = '<span>' . $name . '</span>';
        $htmlTop [] = '</a>';
        $htmlTop [] = '</div>';
        $htmlTop [] = '</div>';
        $this->_topMenu [] = implode ( "\n", $htmlTop );
        if ($drawPopup) {
            $htmlPopup = array ();
            /**
             * Popup function for hide
             */
            $htmlPopup [] = '<div id="popup' . $id . '" class="custom-menu-popup" onmouseout="HideMenuPopup(this, event, \'popup' . $id . '\', \'menu' . $id . '\')" onmouseover="PopupOver(this, event, \'popup' . $id . '\', \'menu' . $id . '\')">';
            if (count ( $activeChildren )) {
                $columns = ( int ) Mage::getStoreConfig ( 'custom_menu/columns/count' );
                $htmlPopup [] = '<div class="block1">';
                $htmlPopup [] = $this->drawColumns ( $activeChildren, $columns );
                $htmlPopup [] = '<div class="clearBoth"></div>';
                $htmlPopup [] = '</div>';
                if ($imageSrc != '') {
                    $htmlPopup [] = '<div class="category-image-menu">';
                    $htmlPopup [] = '<img src="' . Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . 'catalog/category/' . $imageSrc . '"/>';
                    $htmlPopup [] = '</div>';
                }
            }
            
            $htmlPopup = $this->setHtmlPopup ( $blockHtml, $htmlPopup, $blockId );
            
            $htmlPopup [] = '</div>';
            $this->_popupMenu [] = implode ( "\n", $htmlPopup );
        }
    }
    
    /**
     * Get draw popup condition
     *
     * @param string $blockHtml            
     * @param array $activeChildren            
     * @return boolean
     */
    public function getDrawPopup($blockHtml, $activeChildren) {
        return ($blockHtml || count ( $activeChildren ));
    }
    
    /**
     * Set html popup div
     *
     * @param string $blockHtml            
     * @param array $htmlPopup            
     * @param number $blockId            
     * @return string $htmlPopup
     */
    public function setHtmlPopup($blockHtml, $htmlPopup, $blockId) {
        /**
         * draw Custom User Block
         */
        if ($blockHtml) {
            $htmlPopup [] = '<div id="' . $blockId . '" class="block2">';
            $htmlPopup [] = $blockHtml;
            $htmlPopup [] = '</div>';
        }
        return $htmlPopup;
    }
    
    /**
     * Check active category
     *
     * @param array $category            
     * @return string $active
     */
    public function checkActiveCateogry($category) {
        /**
         * Class for active category
         */
        $active = '';
        if ($this->isCategoryActive ( $category )) {
            $active = ' act';
        }
        return $active;
    }
    
    /**
     * Function to display mobile menu
     *
     * Passed the sub menu item
     *
     * @param int $children
     *            Set the hierarchy level
     * @param int $level
     *            This Function will return Header mobile menu display hierarchy for mobile view
     * @return string
     */
    public function drawMobileMenuItem($children, $level = 1) {
        $keyCurrent = $this->getCurrentCategory ()->getId ();
        $html = '';
        foreach ( $children as $child ) {
            /**
             * check condition child value is object and is active equal to true
             */
            if (is_object ( $child ) && $child->getIsActive ()) {
                /**
                 * class for active category
                 */
                
                $id = $child->getId ();
                $activeChildren = $this->_getActiveChildren ( $child, $level );
                $active = (($this->isCategoryActive ( $child )) && ($id == $keyCurrent)) ? ' act' : (($this->isCategoryActive ( $child )) ? ' actParent' : '');
                
                $html .= '<div id="menu-mobile-' . $id . '" class="itemMenu level' . $level . $active . '">';
                /**
                 * format category name
                 */
                $name = $this->escapeHtml ( $child->getName () );
                if (Mage::getStoreConfig ( 'custom_menu/general/non_breaking_space' )) {
                    $name = str_replace ( ' ', '&nbsp;', $name );
                }
                $html .= '<div class="parentMenu">';
                $html .= '<a class="itemMenuName level' . $level . '" href="' . $this->getCategoryUrl ( $child ) . '"><span>' . $name . '</span></a>';
                /**
                 * check condition active children count is greaterthan 0
                 */
                if (count ( $activeChildren ) > 0) {
                    $html .= '<span class="button" rel="submenu-mobile-' . $id . '" onclick="SubMenuToggle(this, \'menu-mobile-' . $id . '\', \'submenu-mobile-' . $id . '\');">&nbsp;</span>';
                }
                /**
                 * check condition active children count is greaterthan 0
                 */
                $html .= '</div>';
                if (count ( $activeChildren ) > 0) {
                    $html .= '<div id="submenu-mobile-' . $id . '" rel="level' . $level . '" class="custom-menu-submenu level' . $level . '" style="display: none;">';
                    $html .= $this->drawMobileMenuItem ( $activeChildren, $level + 1 );
                    $html .= '<div class="clearBoth"></div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
        }
        return $html;
    }
    
    /**
     * Function to display top level menu
     *
     * Passed the sub menu item
     *
     * @param int $children
     *            Set the hierarchy level
     * @param int $level
     *            This Function will return Header mobile menu display hierarchy for mobile view
     * @return string
     */
    public function drawMenuItem($children, $level = 1) {
        $html = '<div class="itemMenu level' . $level . '">';
        $keyCurrent = $this->getCurrentCategory ()->getId ();
        foreach ( $children as $child ) {
            /**
             * check condition child value is object and is active equal to true
             */
            if (is_object ( $child ) && $child->getIsActive ()) {
                /**
                 * class for active category
                 */
                $active = (($this->isCategoryActive ( $child )) && ($child->getId () == $keyCurrent)) ? ' act' : ($this->isCategoryActive ( $child ) ? ' actParent' : '');
                
                /**
                 * format category name
                 */
                $name = $this->escapeHtml ( $child->getName () );
                if (Mage::getStoreConfig ( 'custom_menu/general/non_breaking_space' )) {
                    $name = str_replace ( ' ', '&nbsp;', $name );
                }
                $html .= '<a class="itemMenuName level' . $level . $active . '" href="' . $this->getCategoryUrl ( $child ) . '"><span>' . $name . '</span></a>';
                $activeChildren = $this->_getActiveChildren ( $child, $level );
                /**
                 * check condition active children count is greaterthan 0
                 */
                if (count ( $activeChildren ) > 0) {
                    $html .= '<div class="itemSubMenu level' . $level . '">';
                    $html .= $this->drawMenuItem ( $activeChildren, $level + 1 );
                    $html .= '</div>';
                }
            }
        }
        return $html .= '</div>';
    }
    
    /**
     * Function to split menu by columns
     *
     * Passed the sub menu item
     *
     * @param int $children
     *            Set the $columns
     * @param int $columns
     *            This Function will return the number of columns
     * @return string
     */
    public function drawColumns($children, $columns = 1) {
        $html = '';
        /**
         * explode by columns
         */
        if ($columns < 1) {
            $columns = 1;
        }
        $chunks = $this->_explodeByColumns ( $children, $columns );
        /**
         * draw columns
         */
        $lastColumnNumber = count ( $chunks );
        $i = 1;
        foreach ( $chunks as $key => $value ) {
            /**
             * check condition value count is equal to false
             */
            if (! count ( $value )) {
                continue;
            }
            $class = '';
            /**
             * check condition $i value is equal to 1
             */
            if ($i == 1) {
                $class .= ' first';
            }
            /**
             * check condition $I value is equal to last colum value
             */
            if ($i == $lastColumnNumber) {
                $class .= ' last';
            }
            /**
             * check condition $i value is equal to 0 devided by 2
             */
            if ($i % 2) {
                $class .= ' odd';
            } else {
                $class .= ' even';
            }
            $html .= '<div class="column' . $class . '">';
            $html .= $this->drawMenuItem ( $value, 1 );
            $html .= '</div>';
            $i ++;
        }
        return $html;
    }
    
    /**
     * Function to get the active sub menus in header
     *
     * Passed the parent menu item
     *
     * @param int $parent
     *            Set the $level
     * @param int $level
     *            This Function will return active sub menu items in web
     * @return string
     */
    protected function _getActiveChildren($parent, $level) {
        $activeChildren = array ();
        /**
         * check level
         */
        $maxLevel = ( int ) Mage::getStoreConfig ( 'custom_menu/general/max_level' );
        if (($maxLevel > 0) && ($level >= ($maxLevel - 1))) {
            return $activeChildren;
        }
        /**
         * check level
         */
        if (Mage::helper ( 'catalog/category_flat' )->isEnabled ()) {
            $children = $parent->getChildrenNodes ();
            $childrenCount = count ( $children );
        } else {
            $children = $parent->getChildren ();
            $childrenCount = $children->count ();
        }
        $hasChildren = $children && $childrenCount;
        /**
         * check condition children is equal to true
         */
        if ($hasChildren) {
            foreach ( $children as $child ) {
                if ($this->_isCategoryDisplayed ( $child )) {
                    array_push ( $activeChildren, $child );
                }
            }
        }
        return $activeChildren;
    }
    
    /**
     * Function to display the categories
     *
     * Passed the child menu item
     *
     * @param int $child
     *            This Function will return caregories
     * @return string
     */
    private function _isCategoryDisplayed(&$child) {
        /**
         * check condition children is active is equal to false
         */
        if (! $child->getIsActive ()) {
            return false;
        }
        /**
         * check products count
         */
        /**
         * get collection info
         */
        if (! Mage::getStoreConfig ( 'custom_menu/general/display_empty_categories' )) {
            $data = $this->_getProductsCountData ();
            /**
             * check by id
             */
            $id = $child->getId ();
            /**
             * Mage::log($id); Mage::log($data);
             */
            if (! isset ( $data [$id] ) || ! $data [$id] ['product_count']) {
                return false;
            }
        }
        /**
         * check products count
         */
        return true;
    }
    
    /**
     * Function to get the category wise product count
     *
     * This Function will return product count
     *
     * @return int
     */
    private function _getProductsCountData() {
        /**
         * check condition product count is equal to null
         */
        if (is_null ( $this->_productsCount )) {
            $collection = Mage::getModel ( 'catalog/category' )->getCollection ();
            $storeId = Mage::app ()->getStore ()->getId ();
            /**
             *
             * @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
             */
            $collection->addAttributeToSelect ( 'name' )->addAttributeToSelect ( 'is_active' )->setProductStoreId ( $storeId )->setLoadProductCount ( true )->setStoreId ( $storeId );
            $productsCount = array ();
            foreach ( $collection as $cat ) {
                $productsCount [$cat->getId ()] = array (
                        'name' => $cat->getName (),
                        'product_count' => $cat->getProductCount () 
                );
            }
            /**
             * Mage::log($productsCount);
             */
            $this->_productsCount = $productsCount;
        }
        return $this->_productsCount;
    }
    
    /**
     * Function to split the menu by columns
     *
     * Passed the target column to split the menu
     *
     * @param int $target
     *            Passed the number of column to be split the menu
     * @param int $num
     *            This Function will return caregories
     * @return string
     */
    private function _explodeByColumns($target, $num) {
        if (( int ) Mage::getStoreConfig ( 'custom_menu/columns/divided_horizontally' )) {
            $target = static::_explodeArrayByColumnsHorisontal ( $target, $num );
        } else {
            $target = static::_explodeArrayByColumnsVertical ( $target, $num );
        }
        /**
         * return $target;
         */
        $checkingForTargetAndMenu = Mage::helper ( 'custommenu/navigation' )->checkingForTargetAndMenu ( $target );
        
        if ($checkingForTargetAndMenu == 1) {
            /**
             * combine consistently numerically small column
             */
            /**
             * 1.
             * calc length of each column
             */
            $max = 0;
            $columnsLength = array ();
            foreach ( $target as $key => $child ) {
                $count = 0;
                $this->_countChild ( $child, 1, $count );
                /**
                 * check condition max value lessthan count value
                 */
                $max = Mage::helper ( 'custommenu/navigation' )->getMaxValue ( $max, $count );
                $columnsLength [$key] = $count;
            }
            /**
             * 2.
             * merge small columns with next
             */
            $xColumns = array ();
            $column = array ();
            $cnt = 0;
            $xColumnsLength = array ();
            $k = 0;
            foreach ( $columnsLength as $key => $count ) {
                $cnt += $count;
                /**
                 * check condition count calue greaterthan max value and colum count is not equal empty
                 */
                $checkingForMaxAndCount = Mage::helper ( 'custommenu/navigation' )->checkingForMaxAndCount ( $cnt, $max, $column );
                
                if ($checkingForMaxAndCount == 1) {
                    $xColumns [$k] = $column;
                    $xColumnsLength [$k] = $cnt - $count;
                    $k ++;
                    $column = array ();
                    $cnt = $count;
                }
                $column = array_merge ( $column, $target [$key] );
            }
            $xColumns [$k] = $column;
            $xColumnsLength [$k] = $cnt - $count;
            /**
             * 3.
             * integrate columns of one element
             */
            $target = $xColumns;
            $xColumns = array ();
            $nextKey = - 1;
            /**
             * check condition count calue greaterthan max value and target count is not equal empty
             */
            $checkMaxAndCount = Mage::helper ( 'custommenu/navigation' )->checkMaxAndCount ( $max, $target );
            if ($checkMaxAndCount == 1) {
                $target = Mage::helper ( 'custommenu/navigation' )->getTargetColumns ( $target, $nextKey, $xColumnsLength, $xColumns );
            }
        }
        
        $_rtl = Mage::getStoreConfigFlag ( 'custom_menu/general/rtl' );
        /**
         * check condition rtl value is equal to true
         */
        if ($_rtl) {
            $target = array_reverse ( $target );
        }
        return $target;
    }
    
    /**
     * Function to count the child categories
     *
     * Passed the children category to get the count
     *
     * @param int $children
     *            Passed the count of the children
     * @param int $count
     *            Set the hierarchy level
     * @param int $level
     *            This Function will return caregories
     * @return string
     */
    private function _countChild($children, $level, &$count) {
        foreach ( $children as $child ) {
            /**
             * check condition child active is equal to true
             */
            if ($child->getIsActive ()) {
                $count ++;
                $activeChildren = $this->_getActiveChildren ( $child, $level );
                if (count ( $activeChildren ) > 0) {
                    $this->_countChild ( $activeChildren, $level + 1, $count );
                }
            }
        }
    }
    
    /**
     * Function to split the menu by columns in Horizontal
     *
     * Passed the list of menu
     *
     * @param int $list
     *            Passed the number of column to be split the menu
     * @param int $num
     *            This Function will return caregories
     * @return string
     */
    private static function _explodeArrayByColumnsHorisontal($list, $num) {
        /**
         * check condition num value equal to 0 or lessthan
         */
        if ($num <= 0) {
            return array (
                    $list 
            );
        }
        $partition = array ();
        $partition = array_pad ( $partition, $num, array () );
        $i = 0;
        foreach ( $list as $key => $value ) {
            $partition [$i] [$key] = $value;
            ++ $i;
            if ($i == $num) {
                $i = 0;
            }
        }
        return $partition;
    }
    
    /**
     * Function to split the menu by columns in Vertical
     *
     * Passed the list of menu
     *
     * @param int $list
     *            Passed the number of column to be split the menu
     * @param int $num
     *            This Function will return caregories
     * @return string
     */
    private static function _explodeArrayByColumnsVertical($list, $num) {
        /**
         * check condition num value is equal to 0 or lessthan to 0
         */
        if ($num <= 0) {
            return array (
                    $list 
            );
        }
        $listlen = count ( $list );
        $partlen = floor ( $listlen / $num );
        $partrem = $listlen % $num;
        $partition = array ();
        $mark = 0;
        for($column = 0; $column < $num; $column ++) {
            $incr = ($column < $partrem) ? $partlen + 1 : $partlen;
            $partition [$column] = array_slice ( $list, $mark, $incr );
            $mark += $incr;
        }
        return $partition;
    }
}
