<?php

class Pcarousel_Pcarousel_Block_Adminhtml_Pcarousel_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'pcarousel';
        $this->_controller = 'adminhtml_pcarousel';
        
        $this->_updateButton('save', 'label', Mage::helper('pcarousel')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('pcarousel')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('pcarousel_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'pcarousel_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'pcarousel_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('pcarousel_data') && Mage::registry('pcarousel_data')->getId() ) {
            return Mage::helper('pcarousel')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('pcarousel_data')->getTitle()));
        } else {
            return Mage::helper('pcarousel')->__('Add Item');
        }
    }
}