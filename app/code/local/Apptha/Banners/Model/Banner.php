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

class Apptha_Banners_Model_Banner extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		$this->_init('banners/banner');
	}
	
	/**
	 * Retrieve the group model associated with the banner
	 *
	 * @return Apptha_Banners_Model_Slides|false
	 */
	public function getGroup()
	{
		if (!$this->hasGroup()) {
			$this->setGroup($this->getResource()->getGroup($this));
		}
		
		return $this->getData('group');
	}
	
	/**
	 * Determine whether the banner has a valid URL
	 *
	 * @return bool
	 */
	public function hasUrl()
	{
		return strlen($this->getUrl()) > 1;
	}
	
	/**
	 * Retrieve the alt text
	 * If the alt_text field is empty, use the title field
	 *
	 * @return string
	 */
	public function getAltText()
	{
		return $this->getData('alt_text') ? $this->getData('alt_text') : $this->getTitle();
	}

	/**
	 * Retrieve the image URL
	 *
	 * @return string
	 */
	public function getImageUrl()
	{
		if (!$this->hasImageUrl()) {
			$this->setImageUrl(Mage::helper('banners/image')->getImageUrl($this->getImage()));
		}
		
		return $this->getData('image_url');
	}

	/**
	 * Retrieve the URL
	 * This converts relative URL's to absolute
	 *
	 * @return string
	 */
	public function getUrl()
	{
		if ($this->_getData('url')) {
			if (strpos($this->_getData('url'), 'http://') === false && strpos($this->_getData('url'), 'https://') === false) {
				$this->setUrl(Mage::getBaseUrl() . ltrim($this->_getData('url'), '/ '));
			}
		}
		
		return $this->_getData('url');
	}
}
