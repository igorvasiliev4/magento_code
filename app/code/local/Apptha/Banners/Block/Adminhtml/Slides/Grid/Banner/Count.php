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

class Apptha_Banners_Block_Adminhtml_Slides_Grid_Banner_Count extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
		return $this->_getValue($row);
	}
	
	protected function _getValue(Varien_Object $row){
		
		$val = $row->getData($this->getColumn()->getIndex()); 
		$collection = Mage::getResourceModel('banners/banner_collection')
			->addSlideIdFilter($val);
		return $collection->count();
	}
	
}