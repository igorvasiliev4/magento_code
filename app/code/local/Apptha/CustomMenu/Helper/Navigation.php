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
 * @version     1.6
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */

/**
 * Custom Menu extension Helper file
 */
class Apptha_CustomMenu_Helper_Navigation extends Mage_Core_Helper_Abstract {
    /**
     * Checking for target and menu
     *
     * @param array $target            
     * @return $checkingForTargetAndMenu
     */
    public function checkingForTargetAndMenu($target) {
        $checkingForTargetAndMenu = 0;
        if (( int ) Mage::getStoreConfig ( 'custom_menu/columns/integrate' ) && count ( $target )) {
            $checkingForTargetAndMenu = 1;
        }
        return $checkingForTargetAndMenu;
    }
    
    /**
     * Get max value
     *
     * @param number $max            
     * @param number $count            
     * @param number $max            
     */
    public function getMaxValue($max, $count) {
        if ($max < $count) {
            $max = $count;
        }
        return $max;
    }
    /**
     * Checking for max and count
     *
     * @param number $cnt            
     * @param number $max            
     * @param number $column            
     * @return number $checkingForMaxAndCount
     */
    public function checkingForMaxAndCount($cnt, $max, $column) {
        $checkingForMaxAndCount = 0;
        if ($cnt > $max && count ( $column )) {
            $checkingForMaxAndCount = 1;
        }
        return $checkingForMaxAndCount;
    }
    
    /**
     * Check max and count
     *
     * @param number $max            
     * @param number $target            
     * @return number $checkMaxAndCount
     */
    public function checkMaxAndCount($max, $target) {
        $checkMaxAndCount = 0;
        if ($max > 1 && count ( $target ) > 1) {
            $checkMaxAndCount = 1;
        }
        return $checkMaxAndCount;
    }
    /**
     * Get target columns value
     *
     * @param array $target            
     * @param number $nextKey            
     * @param array $xColumnsLength            
     * @param array $xColumns            
     * @return array $target
     */
    public function getTargetColumns($target, $nextKey, $xColumnsLength, $xColumns) {
        foreach ( $target as $key => $column ) {
            if ($key == $nextKey) {
                continue;
            }
            /**
             * check condition colum length is equal to 1
             */
            if ($xColumnsLength [$key] == 1) {
                /**
                 * merge with next column
                 */
                $nextKey = $key + 1;
                if (isset ( $target [$nextKey] ) && count ( $target [$nextKey] )) {
                    $xColumns [] = array_merge ( $column, $target [$nextKey] );
                    continue;
                }
            }
            $xColumns [] = $column;
        }
        return $xColumns;
    }
}
