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

class Apptha_Banners_Block_View extends Mage_Core_Block_Template
{
	protected  $_head_js = 'apptha_banner/apptha_banner.min.js';
	protected  $_head_css = 'css/apptha_banner/style.css';
	
	const HEAD_ITEM_KEY_JS = 'js/apptha_banner/apptha_banner.min.js';
	const HEAD_ITEM_KEY_CSS = 'skin_css/css/apptha_banner/style.css';
	
	/**
	 * Determine whether a valid group is set
	 *
	 * @return bool
	 */
	public function hasValidSlide()
	{
		if ($this->helper('banners')->isEnabled()) {
			return is_object($this->getSlides());
		}
		
		return false;
	}
	
	
	
	/**
	 * Set the slide code
	 * The group code is validated before being set
	 *
	 * @param string $code
	 */
	public function setSlideCode($code)
	{
		$currentGroupCode = $this->getSlideCode();
		
		if ($currentGroupCode != $code) {
			$this->setSlides(null);
			$this->setData('slide_code', null);

			$slide = Mage::getModel('banners/slides')->loadByCode($code);

			if ($slide->getId() && $slide->getIsEnabled()) {
				if (in_array($slide->getStoreId(), array(0, Mage::app()->getStore()->getId()))) {
					$this->setSlides($slide);
					$this->setData('apptha_banner', $code);
				}
			}
		}
		return $this;
	}

	/**
	 * Retrieve a collection of banners
	 *
	 */
	public function getBanners()
	{
		return $this->getSlides()->getBannerCollection();
	}
	
	/**
	 * If a template isn't passed in the XML, set the default template
	 *
	 */
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();
		
		if (!$this->getTemplate()) {
			$this->setTemplate('apptha_banners/default.phtml');
		}
	
		return $this;	
	}
	
	/**
	 * Ensure the JS and CSS have been included
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		if (($headBlock = $this->getLayout()->getBlock('head')) !== false) {
			$headBlock->addJs($this->_head_js);
			$headBlock->addCss($this->_head_css);
		}
		
		return parent::_prepareLayout();
	}	
}
