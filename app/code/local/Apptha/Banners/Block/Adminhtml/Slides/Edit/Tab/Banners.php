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

class Apptha_Banners_Block_Adminhtml_Slides_Edit_Tab_Banners extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('banner_grid');
		$this->setDefaultSort('title');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	/**
	 * Initialise and set the collection for the grid
	 *
	 */
	protected function _prepareCollection()
	{
		if($this->getSlideId()){
			$slide_id = $this->getSlideId();
		}else{
			$slide_id = $this->getRequest()->getParam('slide_id');
		}
		$collection = Mage::getResourceModel('banners/banner_collection')
			->addSlideIdFilter($slide_id);

		$this->setCollection($collection);
	
		return parent::_prepareCollection();
	}
	
	/**
	 * Add the columns to the grid
	 *
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'	=> $this->__('ID'),
			'align'		=> 'left',
			'width'		=> '60px',
			'index'		=> 'id',
		));

		$this->addColumn('title', array(
			'header'		=> $this->__('Title'),
			'align'			=> 'left',
			'index'			=> 'title',
		));
        
		$this->addColumn('is_enabled', array(
			'header'	=> $this->__('Enabled'),
			'width'		=> '90px',
			'index'		=> 'is_enabled',
			'type'		=> 'options',
			'options'	=> array(
				1 => $this->__('Enabled'),
				0 => $this->__('Disabled'),
			),
		));

		return parent::_prepareColumns();
	}
	
	/**
	 * Retrieve the URL used to modify the grid via AJAX
	 *
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/bannerGrid',array('slide_id'=> $this->getSlideId(), '_current' => true));
	}
	
	/**
	 * Retrieve the URL for the row
	 *
	 */
	public function getRowUrl($row)
	{
		//return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	/**
	 * Retrieve the group ID
	 *
	 * @return int
	 */
	public function getSlideId()
	{
		return Mage::registry('banners_slides') ? Mage::registry('banners_slides')->getId() : 0;
	}
}
