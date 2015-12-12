<?php

class Magehit_Faq_Block_Adminhtml_Faq_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('faq_form', array('legend'=>Mage::helper('faq')->__('FAQ Category Information')));
     
      $fieldset->addField('faqname', 'text', array(
          'label'     => Mage::helper('faq')->__('FAQ Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'faqname',
      ));
      
      $fieldset->addField('faq_order', 'text', array(
          'label'     => Mage::helper('faq')->__('Order'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'faq_order',
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('faq')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('faq')->__('Enabled'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('faq')->__('Disabled'),
              ),
          ),
      ));
       if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('faq')->__('Description'),
          'title'     => Mage::helper('faq')->__('Description'),
          'style'     => 'width:500px; height:200px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getFaqData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFaqData());
          Mage::getSingleton('adminhtml/session')->setFaqData(null);
      } elseif ( Mage::registry('faq_data') ) {
          $form->setValues(Mage::registry('faq_data')->getData());
      	  if($faq=Mage::registry('faq_data')->getId()){
      	  		if (!Mage::app()->isSingleStoreMode()) {
				$collection =  Mage::getModel('faq/faq')->getCollection();
				$collection->join('faq_store', 'faq_store.faq_id = main_table.faq_id AND main_table.faq_id='.$faq, 'faq_store.store_id');
				
				$arrStoreId = array();
		        foreach($collection->getData() as $col){
		        	$arrStoreId[] = $col['store_id'];	
		        }
	        
	         	// set value for store view selected:
	         	$form->getElement('store_id')->setValue($arrStoreId);
      	  		}
			}
      }
      return parent::_prepareForm();
  }
}