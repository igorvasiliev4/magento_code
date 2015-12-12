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
class MT_Export_Block_Adminhtml_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('widgetInstanceGrid');
      $this->setDefaultSort('instance_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('widget/widget_instance')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('instance_id', array(
          'header'    => Mage::helper('widget')->__('Widget ID'),
          'align'     => 'left',
          'index'     => 'instance_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('widget')->__('Widget Instance Title'),
          'align'     => 'left',
          'index'     => 'title',
      ));

      $this->addColumn('type', array(
          'header'    => Mage::helper('widget')->__('Type'),
          'align'     => 'left',
          'index'     => 'instance_type',
          'type'      => 'options',
          'options'   => $this->getTypesOptionsArray()
      ));

      $this->addColumn('package_theme', array(
          'header'    => Mage::helper('widget')->__('Design Package/Theme'),
          'align'     => 'left',
          'index'     => 'package_theme',
          'type'      => 'theme',
          'with_empty' => true,
      ));

      $this->addColumn('sort_order', array(
          'header'    => Mage::helper('widget')->__('Sort Order'),
          'width'     => '100',
          'align'     => 'center',
          'index'     => 'sort_order',
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
      $this->setMassactionIdField('instance_id');
      $this->getMassactionBlock()->setFormFieldName('instance_ids');

      $this->getMassactionBlock()->addItem('export', array(
          'label'    => Mage::helper('export')->__('Export'),
          'url'      => $this->getUrl('*/*/exportXml'),
          'confirm'  => Mage::helper('adminhtml')->__('Are you sure?')
      ));
	  return $this;
  }

}