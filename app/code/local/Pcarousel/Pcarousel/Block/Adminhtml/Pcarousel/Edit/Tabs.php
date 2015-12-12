<?php

class Pcarousel_Pcarousel_Block_Adminhtml_Pcarousel_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('pcarousel_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('pcarousel')->__('Carousel Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('pcarousel')->__('Carousel Information'),
          'title'     => Mage::helper('pcarousel')->__('Carousel Information'),
          'content'   => $this->getLayout()->createBlock('pcarousel/adminhtml_pcarousel_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}