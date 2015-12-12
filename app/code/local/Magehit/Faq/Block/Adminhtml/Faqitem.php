<?php
class Magehit_Faq_Block_Adminhtml_Faqitem extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_faqitem';
    $this->_blockGroup = 'faq';
    $this->_headerText = Mage::helper('faq')->__('FAQ Item Manager');
    $this->_addButtonLabel = Mage::helper('faq')->__('Add FAQ Item');
    parent::__construct();
  }
}