<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package      MT_Export
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       ZooExtension.com
 * @email        magento@cleversoft.co
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class MT_Export_Block_Adminhtml_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('cmsBlockGrid');
      $this->setDefaultSort('block_identifier');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('cms/block')->getCollection();
      /* @var $collection Mage_Cms_Model_Mysql4_Block_Collection */
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $baseUrl = $this->getUrl();

      $this->addColumn('title', array(
          'header'    => Mage::helper('cms')->__('Title'),
          'align'     => 'left',
          'index'     => 'title',
      ));

      $this->addColumn('identifier', array(
          'header'    => Mage::helper('cms')->__('Identifier'),
          'align'     => 'left',
          'index'     => 'identifier'
      ));

      if (!Mage::app()->isSingleStoreMode()) {
          $this->addColumn('store_id', array(
              'header'        => Mage::helper('cms')->__('Store View'),
              'index'         => 'store_id',
              'type'          => 'store',
              'store_all'     => true,
              'store_view'    => true,
              'sortable'      => false,
              'filter_condition_callback'
              => array($this, '_filterStoreCondition'),
          ));
      }

      $this->addColumn('is_active', array(
          'header'    => Mage::helper('cms')->__('Status'),
          'index'     => 'is_active',
          'type'      => 'options',
          'options'   => array(
              0 => Mage::helper('cms')->__('Disabled'),
              1 => Mage::helper('cms')->__('Enabled')
          ),
      ));

      $this->addColumn('creation_time', array(
          'header'    => Mage::helper('cms')->__('Date Created'),
          'index'     => 'creation_time',
          'type'      => 'datetime',
      ));

      $this->addColumn('update_time', array(
          'header'    => Mage::helper('cms')->__('Last Modified'),
          'index'     => 'update_time',
          'type'      => 'datetime',
      ));
	  
      return parent::_prepareColumns();
  }
  
  protected function _afterLoadCollection()
  {
	  $this->getCollection()->walk('afterLoad');
	  parent::_afterLoadCollection();
  }
  
  protected function _filterStoreCondition($collection, $column)
  {
	  if (!$value = $column->getFilter()->getValue()) {
		  return;
	  }

	  $this->getCollection()->addStoreFilter($value);
  }

  protected function _prepareMassaction()
  {
      $this->setMassactionIdField('block_id');
      $this->getMassactionBlock()->setFormFieldName('block_ids');

      $this->getMassactionBlock()->addItem('export', array(
          'label'    => Mage::helper('export')->__('Export'),
          'url'      => $this->getUrl('*/*/exportXml'),
          'confirm'  => Mage::helper('adminhtml')->__('Are you sure?')
      ));
	  return $this;
  }

}