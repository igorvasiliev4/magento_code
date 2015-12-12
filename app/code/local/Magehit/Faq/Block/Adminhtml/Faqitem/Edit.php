<?php

class Magehit_Faq_Block_Adminhtml_Faqitem_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'faq';
        $this->_controller = 'adminhtml_faqitem';
        
        $this->_updateButton('save', 'label', Mage::helper('faq')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('faq')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('faqitem_answer') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'faqitem_answer');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'faqitem_answer');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('faqitem_data') && Mage::registry('faqitem_data')->getId() ) {
            return Mage::helper('faq')->__("Edit FAQ Item"); 
            //'%s'", $this->htmlEscape(Mage::registry('faqitem_data')->getFaqname()));
        } else {
            return Mage::helper('faq')->__('Add FAQ Item');
        }
    }
}