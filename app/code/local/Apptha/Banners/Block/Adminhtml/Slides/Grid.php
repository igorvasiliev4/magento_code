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

class Apptha_Banners_Block_Adminhtml_Slides_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('banners_slides_grid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	/**
	 * Insert the Add New button
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		$this->setChild('add_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('adminhtml')->__('Add New Slides'),
					'class' => 'add',
					'onclick'   => "setLocation('" . $this->getUrl('*/banners_slides/new') . "');",
				))
		);
				
		return parent::_prepareLayout();
	}
	
	/**
	 * Retrieve the main buttons html
	 *
	 * @return string
	 */
	public function getMainButtonsHtml()
	{
		return parent::getMainButtonsHtml()
			. $this->getChildHtml('add_button');
	}

	/**
	 * Initialise and set the collection for the grid
	 *
	 */
	protected function _prepareCollection()
	{
		$this->setCollection(Mage::getResourceModel('banners/slides_collection'));
	
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
		
		$this->addColumn('slide_code', array(
			'header'	=> $this->__('Code'),
			'align'		=> 'left',
			'index'		=> 'slide_code',
		));
		
		$this->addColumn('banner_count', array(
			'header'	=> $this->__('Banner used'),
			'align'		=> 'left',
			'index'		=> 'id',
			'renderer' => 'Apptha_Banners_Block_Adminhtml_Slides_Grid_Banner_Count'
		));
		
		if (!Mage::app()->isSingleStoreMode()) {
			
			$this->addColumn('store_id', array(
				'header'	=> $this->__('Store'),
				'align'		=> 'left',
				'index'		=> 'store_id',
				'type'		=> 'options',
				'options' 	=> $this->_getStores(),
			));
		}
		
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
	
		$this->addColumn('action',
			array(
				'width'     => '50px',
				'type'      => 'action',
				'getter'     => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('catalog')->__('Edit'),
						'url'     => array(
						'base'=>'*/banners_slides/edit',
					),
					'field'   => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'align' 	=> 'center',
			));
			
		$this->addColumn('getcode',
			array(
				'width'     => '100px',
				'type'      => 'action',
				'getter'     => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('catalog')->__('Get Code'),
						'url'     => array(
										'base'=>'*/banners_slides/getcode',
									),
						'field'   => 'id'
						)
				),
				'filter'    => false,
				'sortable'  => false,
				'align' 	=> 'center',
				'column_css_class' =>'apptha_getCode',
			));	

		return parent::_prepareColumns();
	}
	
	/**
	 * Get the current URL for the grid
	 *
	 * @return string
	 */
	public function getCurrentUrl($params = array())
	{
		return $this->getUrl('*/*/slidesGrid');
	}
	
	/**
	 * Retrieve the URL for the row
	 *
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/banners_slides/edit', array('id' => $row->getId()));
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
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('slides');
	
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> $this->__('Delete'),
			'url'  => $this->getUrl('*/banners_slides/massDelete'),
			'confirm' => Mage::helper('catalog')->__('Are you sure?')
		));
	}
}