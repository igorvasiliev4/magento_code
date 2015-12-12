<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Filter_Model_Resource_Layer_Filter_Price extends Mage_Catalog_Model_Resource_Layer_Filter_Price{
    /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param int $range
     * @param int $index    the range factor
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Price
     */
    public function applyFilterToCollection($filter, $range, $index){
        $collection = $filter->getLayer()->getProductCollection();
        $collection->addPriceData($filter->getCustomerGroupId(), $filter->getWebsiteId());

        $select     = $collection->getSelect();
        $response   = $this->_dispatchPreparePriceEvent($filter, $select);

        $table      = $this->_getIndexTableAlias();
        $additional = join('', $response->getAdditionalCalculations());
        $rate       = $filter->getCurrencyRate();
        $priceExpr  = new Zend_Db_Expr("(({$table}.min_price {$additional}) * {$rate})");

        $filters    = $filter->getPriceRangeCustom();
        if ($filters){
            $select
                ->where($priceExpr . ' >= ?', (int)$filters[0])
                ->where($priceExpr . ' < ?', (int)$filters[1]);
        }else{
            $select
                ->where($priceExpr . ' >= ?', ($range * ($index - 1)))
                ->where($priceExpr . ' < ?', ($range * $index));
        }

        return $this;
    }
}
