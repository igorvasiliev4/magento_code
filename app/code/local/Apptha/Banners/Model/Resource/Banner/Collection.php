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
class Apptha_Banners_Model_Resource_Banner_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('banners/banner');
	}

	/**
	 * Init collection select
	 *
	*/
	protected function _initSelect()
	{
		$this->getSelect()->from(array('main_table' => $this->getMainTable()));
		
		return $this;
	}
	
	/**
	 * Filter the collection by a slide ID
	 *
	 * @param int $groupId
	 */
	public function addSlideIdFilter($slideId)
	{
		return $this->join(
					array('pivot' => 'banners/bannerSlide'), 
					"main_table.id = pivot.banner_id AND pivot.slide_id = {$slideId}",
					array('*')
				);
	}
	
	/**
	 * Filter the collection by enabled banners
	 *
	 * @param int $isEnabled = true
	 */
	public function addIsEnabledFilter($isEnabled = true)
	{
		return $this->addFieldToFilter('is_enabled', $isEnabled ? 1 : 0);
	}
	
	/**
	 * Add a random order to the banners
	 *
	*/
	public function addOrderByRandom($dir = 'ASC')
	{
		$this->getSelect()->order('RAND() ' . $dir);
		return $this;
	}
	
	/**
	 * Add order by sort order
	 *
	*/
	public function addOrderBySortOrder($dir = 'ASC')
	{
		$this->getSelect()->order('sort_order ' . $dir);
		return $this;
	}
}
