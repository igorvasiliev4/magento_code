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
* @package     Apptha_Mageshop
* @version     0.1.0
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
*
*/

class Apptha_Banners_Block_Popular extends Mage_Catalog_Block_Product_New
{
	/*
	 * Get Popular products in magento
	 * 
	 * @param int $totalPerPage
	 * @param int $storeId
	 * 
	 * @return Object
	 */
	public function getPopular($totalPerPage=20, $storeId=0){
	
		$_productCollection = Mage::getResourceModel('reports/product_collection')
							->addAttributeToSelect('*')							
							->setStoreId($storeId)							
							->addStoreFilter($storeId)							
							->addViewsCount()
							->setPageSize($totalPerPage);
		
		return $_productCollection;
	
	}
}
