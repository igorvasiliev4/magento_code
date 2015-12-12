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

class MagenThemes_MTTheme_Helper_Grid extends Mage_Core_Helper_Abstract
{
    /**
     * Values: number of columns / grid item width
     *
     * @var array
     */
    protected $_itemWidth = array(
        "1" => 99,
        "2" => 49,
        "3" => 32,
        "4" => 23.499,
        "5" => 18.4,
        "6" => 15,
        "7" => 12.5555,
        "8" => 10.752,
    );

    /**
     * Get CSS for grid item based on number of columns
     *
     * @param int $columnCount
     * @return string
     */
    public function getCssGridItem($columnCount)
    {
        $out = "\n";
        $out .= '.itemgrid.itemgrid-adaptive .item { width:' . $this->_itemWidth[$columnCount] . '%; clear:none !important; }' . "\n";

        if ($columnCount > 1)
        {
            $out .= '.itemgrid.itemgrid-adaptive .item:nth-child(' . $columnCount . 'n+1) { clear:left !important; }' . "\n";
        }

        return $out;
    }
}
