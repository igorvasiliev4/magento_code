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

class Apptha_Banners_Model_BannerSlide extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		$this->_init('banners/bannerSlide');
	}
	
	/**
	 * Load the model based on the code field
	 *
	 * @param string $code
	 * @return Apptha_Banners_Model_Slides
	 */
	public function loadByCode($code)
	{
		return $this->load($code, 'slide_code');
	}
	
	/**
	 * Determine whether the slide is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->getIsEnabled();
	}
	
	/**
	 * Retrieve a collection of banners associated with this group
	 *
	 * @return Apptha_Banners_Model_Resource_Banner_Slides
	 */
	public function getBannerCollection()
	{
		if (!$this->hasBannerCollection()) {
			$this->setBannerCollection($this->getResource()->getBannerCollection($this));
		}
		
		return $this->_getData('banner_collection');
	}
	
	/**
	 * Retrieve the amount of banners in this group
	 *
	 * @return int
	 */
	public function getBannerCount()
	{
		if (!$this->hasBannerCount()) {
			$this->setBannerCount($this->getBannerCollection()->count());
		}
		
		return $this->_getData('banner_count');
	}
	
	/**
	 * Determine whether autoplay is enabled
	 *
	 * @return bool
	 */
	public function isAutoplayEnabled()
	{
		return $this->getAutoplay() ? true : false;
	}
	
	/**
	 * Retrieve the No of Item's displayed at a time in the slide
	 *
	 * @return int
	 */
	public function getItemsCount()
	{
		if ($this->_getData('items_count') == '') {
			$this->setItemsCount(1);
		}
		
		return (int)$this->_getData('items_count');
	}
	
	/**
	 * Retrieve the slider speed for this slide
	 *
	 * @return int
	 */
	public function getSlideSpeed()
	{
		if (!$this->_getData('slide_speed')) {
			$this->setSlideSpeed(200);
		}

		return (int)$this->_getData('slide_speed');
	}
	
	/**
	 * Determine whether pagination is enabled
	 *
	 * @return bool
	 */
	public function isPaginationEnabled()
	{
		return $this->getPagination() ? true : false;
	}	
	
	/**
	 * Retrieve the slider effect for this slide
	 *
	 * @return string
	 */
	public function getSliderEffect()
	{
		if (!$this->_getData('slider_effect')) {
			$this->setSliderEffect('fade');
		}
		
		return $this->_getData('slider_effect');
	}

	
	/**
	  * Retrieve Slide data
	  * This is used to popular the Adminhtml form
	  *
	  * @return array
	  */
	public function getSlideSettings()
	{
		return array(
			'autoplay' => (int)$this->isAutoplayEnabled(),
			'items_count' => $this->getItemsCount(),
			'slide_speed' => $this->getSlideSpeed(),
			'pagination' => (int)$this->isPaginationEnabled(),
			'slider_effect' => $this->getSliderEffect(),
		);
	}
}
