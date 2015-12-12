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
* @package     Apptha_Banners
* @version     0.1.0
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
*
*/
class Apptha_Banners_Model_Resource_BannerSlide_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('banners/bannerSlide');
	}
	
	/**
	 * Filter the collection by  slide and banners value
	 *
	 * @param int $slide_id
	 * @param int $banner_id
	 */
	public function getPivotValue($slide_id,$banner_id){
		
		return $this->addFieldToFilter('slide_id', $slide_id)
					->addFieldToFilter('banner_id', $banner_id)
					->getFirstItem();
	}
	
	/**
	 * Filter the collection by  slide and banners value
	 *
	 * @param int $slide_id
	 * @param int $banner_id
	 */
	public function getValueByBanner($banner_id){
		
		return $this->addFieldToFilter('banner_id', $banner_id);
	}
	
	/**
	 * Delete all the entities in the collection
	 *
	 * @todo make batch delete directly from collection
	 */
	public function delete()
	{
	    foreach ($this->getItems() as $k=>$item) {
	        $item->delete();
	        unset($this->_items[$k]);
	    }
	    return $this;
	}
}
