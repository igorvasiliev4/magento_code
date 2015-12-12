<?php

class Magehit_Faq_Block_Adminhtml_Faq_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'faq';
        $this->_controller = 'adminhtml_faq';
        
        $this->_updateButton('save', 'label', Mage::helper('faq')->__('Save FAQ Category'));
        $this->_updateButton('delete', 'label', Mage::helper('faq')->__('Delete FAQ Category'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('faq_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'faq_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'faq_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('faq_data') && Mage::registry('faq_data')->getId() ) {
            return Mage::helper('faq')->__("Edit FAQ Category ");
        } else {
            return Mage::helper('faq')->__('Add FAQ Category');
        }
    }
}