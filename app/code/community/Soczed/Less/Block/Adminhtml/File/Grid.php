<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Soczed
 * @package    Soczed_Less
 * @copyright  Copyright (c) 2012 Soczed <magento@soczed.com> (Benoît Leulliette <benoit@soczed.com>)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Soczed_Less_Block_Adminhtml_File_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('SL_LessFileGrid')
            ->setSaveParametersInSession(true)
            ->setUseAjax(false);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('less/file')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('file', array(
            'header' => $this->__('ID'),
            'index'  => 'file_id',
            'type'   => 'number',
            'width'  => '50px',
        ));
        
        $this->addColumn('path', array(
            'header' => $this->__('Path'),
            'index'  => 'path',
            'width'  => '30%',
        ));
        
        $this->addColumn('cache', array(
            'header'   => $this->__('Cache'),
            'index'    => 'cache',
            'width'    => '10%',
            'filter'   => 'less/adminhtml_widget_grid_column_filter_cache',
            'renderer' => 'less/adminhtml_widget_grid_column_renderer_cache',
        ));
        
        $this->addColumn('custom_variables', array(
            'header'   => $this->__('Variables'),
            'index'    => 'custom_variables',
            'width'    => '30%',
            'filter'   => 'less/adminhtml_widget_grid_column_filter_variables',
            'renderer' => 'less/adminhtml_widget_grid_column_renderer_variables',
        ));
        
        $yesNo = array(
            '1' => $this->__('Yes'),
            '0' => $this->__('No'),
        );
        
        $this->addColumn('use_global_variables', array(
            'header'  => $this->__('Use Global Variables'),
            'index'   => 'use_global_variables',
            'width'   => '10%',
            'type'    => 'options',
            'options' => $yesNo,
        ));
        
        $this->addColumn('force_global_variables', array(
            'header'  => $this->__('Force Global Variables'),
            'index'   => 'force_global_variables',
            'width'   => '10%',
            'type'    => 'options',
            'options' => $yesNo,
        ));
        
        $this->addColumn('force_rebuild', array(
            'header'  => $this->__('Force Rebuild'),
            'index'   => 'force_rebuild',
            'width'   => '10%',
            'type'    => 'options',
            'options' => $yesNo,
        ));
        
        $this->addColumn('action',
            array(
                'header'  => $this->__('Actions'),
                'width'   => '120px',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => $this->__('Edit'),
                        'field'   => 'id',
                        'url'     => array(
                            'base' => '*/*/edit',
                        ),
                    ),
                    array(
                        'caption' => $this->__('Delete'),
                        'confirm' => $this->__('Are you sure?'),
                        'field'   => 'id',
                        'url'     => array(
                            'base' => '*/*/delete',
                        ),
                    ),
                    array(
                        'caption' => $this->__('Reset Cache'),
                        'confirm' => $this->__('Are you sure?'),
                        'field'   => 'id',
                        'url'     => array(
                            'base' => '*/*/reset',
                        ),
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'id',
        ));
        
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getFileId()));
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('less_file');
        
        $this->getMassactionBlock()->addItem('mass_delete', array(
            'label'   => $this->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete', array('_current' => true)),
            'confirm' => $this->__('Are you sure?'),
        ));
        $this->getMassactionBlock()->addItem('mass_reset', array(
            'label'   => $this->__('Reset Cache'),
            'url'     => $this->getUrl('*/*/massReset', array('_current' => true)),
            'confirm' => $this->__('Are you sure?'),
        ));
        
        return $this;
    }
}