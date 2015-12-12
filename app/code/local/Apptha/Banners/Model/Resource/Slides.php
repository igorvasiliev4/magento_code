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

class Apptha_Banners_Model_Resource_Slides extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
	{
		$this->_init('banners/slides', 'id');
	}

	/**
	 * Retrieve the load select object
	 *
	 * @param string $field
	 * @param mixed $value
	 * @param Mage_Core_Model_Abstract $object
	 * @return Varien_Db_Select
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = parent::_getLoadSelect($field, $value, $object);
		
		if (!Mage::app()->isSingleStoreMode() && Mage::app()->getStore()->getId() > 0) {
			$select->where('store_id IN (?)', array(0, Mage::app()->getStore()->getId()))
				->order('store_id DESC')
				->limit(1);
		}
	
		return $select;
	}

	/**
	 * Retrieve a collection of banners associated with the slide
	 *
	 * @param Apptha_Banners_Model_Slides $group
	 * @return Apptha_Banners_Model_Resource_Banner_Collection
	 */
	public function getBannerCollection(Apptha_Banners_Model_Slides $slide, $includeDisabled = false)
	{
		$banners = Mage::getResourceModel('banners/banner_collection')
			->addSlideIdFilter($slide->getId());
			
		if ($slide->getRandomiseBanners()) {
			$banners->addOrderByRandom();
		}
		if (!$includeDisabled) {
			$banners->addIsEnabledFilter(1);
		}
		
		return $banners;
	}
	
	/**
	 * Apply processing before saving object
	 *
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if (!$object->getSlideCode()) {
			throw new Exception(Mage::helper('banners')->__('Banner Slides must have a unique code'));
		}
		
		$object->setSlideCode($this->formatSlideCode($object->getSlideCode()));
		
		if (Mage::getDesign()->getArea() == 'adminhtml') {
			foreach($object->getData() as $field => $value) {
				if (preg_match("/^use_config_([a-zA-Z_]{1,})$/", $field, $result)) {

					$object->setData($result[1], null);
					$object->unsetData($field);
				}
			}
		}
		
		return parent::_beforeSave($object);
	}
	
	/**
	 * Convert a string into a valid slide code
	 *
	 * @param string $str
	 * @return string
	 */
	public function formatSlideCode($str)
	{
		$str = preg_replace('#[^0-9a-z]+#i', '_', Mage::helper('catalog/product_url')->format($str));
		$str = strtolower($str);
		$str = trim($str, '_');
		
		return $str;
	}
}
