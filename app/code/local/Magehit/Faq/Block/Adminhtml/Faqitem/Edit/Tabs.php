<?php

class Magehit_Faq_Block_Adminhtml_Faqitem_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('faqitem_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('faq')->__('FAQ Item Information'));
  }

  /*protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('faq')->__('General'),
          'title'     => Mage::helper('faq')->__('General'),
          'content'   => $this->getLayout()->createBlock('faq/adminhtml_faqitem_edit_tab_form')->toHtml(),
      ));
     
      $this->addTab('content_section', array(
          'label'     => Mage::helper('faq')->__('Content'),
          'title'     => Mage::helper('faq')->__('Content'),
          'content'   => $this->getLayout()->createBlock('faq/adminhtml_faqitem_edit_tab_content')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }*/
}