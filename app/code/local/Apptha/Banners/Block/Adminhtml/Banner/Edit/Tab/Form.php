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

class Apptha_Banners_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Retrieve Additional Element Types
	 *
	 * @return array
	*/
	protected function _getAdditionalElementTypes()
	{
		return array(
			'image' => Mage::getConfig()->getBlockClassName('banners/adminhtml_banner_helper_image')
		);
	}
	
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('banner_');
        $form->setFieldNameSuffix('banner');
        
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('banner_general', array('legend'=> $this->__('General Information')));

		$this->_addElementTypes($fieldset);

		$fieldset->addField('slide_id', 'multiselect', array(
			'name'			=> 'slideids[]',
			'label'			=> $this->__('Slide'),
			'title'			=> $this->__('Slide'),
			'required'		=> true,
			'class'			=> 'required-entry',
			'values'		=> $this->_getSlides()
		));

		$fieldset->addField('title', 'text', array(
			'name' 		=> 'title',
			'label' 	=> $this->__('Title'),
			'title' 	=> $this->__('Title'),
			'required'	=> true,
			'class'		=> 'required-entry',
		));
		
		$fieldset->addField('url', 'text', array(
			'name' 		=> 'url',
			'label' 	=> $this->__('URL'),
			'title' 	=> $this->__('URL')
		));
		
		$fieldset->addField('alt_text', 'text', array(
			'name' 		=> 'alt_text',
			'label' 	=> $this->__('ALT Text'),
			'title' 	=> $this->__('ALT Text'),
		));
		
		$fieldset->addField('html', 'editor', array(
			'name' 		=> 'html',
			'label' 	=> $this->__('HTML'),
			'title' 	=> $this->__('HTML'),
			'style'		=> 'height: 120px; width: 98%;',
		));

		$fieldset->addField('image', 'image', array(
			'name' 		=> 'image',
			'label' 	=> $this->__('Image'),
			'title' 	=> $this->__('Image'),
			'note'		=> $this->__('Image size - 1100 X 480'),
			'required'	=> true,
			'class'		=> 'required-entry',
		));
		
		$fieldset->addField('is_enabled', 'select', array(
			'name' => 'is_enabled',
			'title' => $this->__('Enabled'),
			'label' => $this->__('Enabled'),
			'required' => true,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));

		if ($banner = Mage::registry('banners_banner')) {
			$data = $banner->getData();
			$data['slide_id'] = $this->_getBannerSlides($banner->getId());
			$form->setValues($data);
		}

		return parent::_prepareForm();
	}

	/**
	 * Retrieve an array of all of the slides
	 *
	 * @return array
	 */
	protected function _getSlides()
	{
		$groups = Mage::getResourceModel('banners/slides_collection');
		$options = array();
		
		foreach($groups as $group) {
			$options[] = array('value'=>$group->getId(),'label' => $group->getTitle());
		}
		return $options;
	}
	
	/**
	 * Retrieve an array of all of the slids related to banner
	 *
	 * @return array
	 */
	protected function _getBannerSlides($banner_id)
	{
		$groups = Mage::getModel('banners/bannerSlide')->getCollection()->addFieldToFilter('banner_id',$banner_id);
	
		$options = array();
		foreach($groups as $group) {
			$options[] = (int)$group->slide_id;
		}
		return $options;
	}
}
