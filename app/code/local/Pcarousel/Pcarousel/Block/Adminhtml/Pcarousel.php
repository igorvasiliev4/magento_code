<?php
class Pcarousel_Pcarousel_Block_Adminhtml_Pcarousel extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_pcarousel';
    $this->_blockGroup = 'pcarousel';
    $this->_headerText = Mage::helper('pcarousel')->__('Carousel Manager');
    $this->_addButtonLabel = Mage::helper('pcarousel')->__('Add Product Carousel');
    parent::__construct();
  }
}