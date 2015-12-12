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

class Apptha_Banners_Block_Adminhtml_Slides_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('slides_');
        $form->setFieldNameSuffix('slides');
        
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('slides_general', array('legend'=> $this->__('General Information')));

		$fieldset->addField('title', 'text', array(
			'name' 		=> 'title',
			'label' 	=> $this->__('Title'),
			'title' 	=> $this->__('Title'),
			'required'	=> true,
			'class'		=> 'required-entry',
		));
		
		$fieldset->addField('slide_code', 'text', array(
			'name' 		=> 'slide_code',
			'label' 	=> $this->__('Code'),
			'title' 	=> $this->__('Code'),
			'note'		=> $this->__('This is a unique identifier that is used to inject the Slider via XML'),
			'required'	=> true,
			'class'		=> 'required-entry validate-code',
		));

		$fieldset->addField('randomise_banners', 'select', array(
			'name' => 'randomise_banners',
			'comment' => $this->__('This is for groups with more than 1 banner in'),
			'title' => $this->__('Randomise Banner Position'),
			'label' => $this->__('Randomise Banner Position'),
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('is_enabled', 'select', array(
			'name' => 'is_enabled',
			'title' => $this->__('Enabled'),
			'label' => $this->__('Enabled'),
			'required' => true,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));

		$fieldset->addField('store_id', 'select', array(
			'name'		=> 'store_id',
			'label'		=> $this->__('Store'),
			'title'		=> $this->__('Store'),
			'required'	=> true,
			'class'		=> 'required-entry',
			'values'	=> $this->_getStores()
		));

		$fieldset = $form->addFieldset('Slide_settings', array('legend'=> $this->__('Slider Settings')));
		
		$fieldset->addField('autoplay', 'select', array(
			'name' => 'autoplay',
			'title' => $this->__('Enable Autoplay'),
			'label' => $this->__('Enable Autoplay'),
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));

		
		$fieldset->addField('items_count', 'text', array(
			'name' => 'items_count',
			'title' => $this->__('No of Item\'s displayed at a time'),
			'label' => $this->__('No of Item\'s displayed at a time'),
			'class'	=> 'validate-greater-than-zero',
		));

		$fieldset->addField('slide_speed', 'text', array(
			'name' => 'slide_speed',
			'title' => $this->__('Slide speed in milliseconds'),
			'label' => $this->__('Slide speed in milliseconds'),
			'class'	=> 'validate-greater-than-zero',
			'note'		=> $this->__('The Default value is 200'),
		));
		
		$fieldset->addField('pagination', 'select', array(
			'name' => 'pagination',
			'title' => $this->__('Enable Pagination'),
			'label' => $this->__('Enable Pagination'),
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('slider_effect', 'select', array(
			'name' => 'slider_effect',
			'title' => $this->__('Effect Style'),
			'label' => $this->__('Effect Style'),
			'values' => Mage::getModel('banners/system_config_source_slider_effect')->toOptionArray(),
		));
		

		if ($slides = Mage::registry('banners_slides')) {
			$form->setValues($slides->getData());
		}
		else {
			$form->setValues(Mage::getModel('banners/slides')->getSlideSettings());
		}

		return parent::_prepareForm();
	}

	/**
	 * Retrieve an array of all of the stores
	 *
	 * @return array
	 */
	protected function _getStores()
	{
		$stores = Mage::getResourceModel('core/store_collection');
		$options = array(0 => $this->__('Global'));
		
		foreach($stores as $store) {
			$options[$store->getId()] = $store->getWebsite()->getName() . ': ' . $store->getName();
		}

		return $options;
	}
}
