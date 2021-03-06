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

class Apptha_Banners_Block_Adminhtml_Slides_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('banners_slides_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Apptha Banners / Slides'));
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab('general',
			array(
				'label' => $this->__('General'),
				'title' => $this->__('General'),
				'content' => $this->getLayout()->createBlock('banners/adminhtml_slides_edit_tab_form')->toHtml(),
			)
		);
		
		if (Mage::registry('banners_slides')) {
			$this->addTab('banners',
				array(
					'label' => $this->__('Banners'),
					'title' => $this->__('Banners'),
					'content' => $this->getLayout()->createBlock('banners/adminhtml_slides_edit_tab_banners')->toHtml(),
				)
			);
		}
		
		return parent::_beforeToHtml();
	}
}
