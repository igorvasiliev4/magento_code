<?php

class Pcarousel_Pcarousel_Block_Adminhtml_Pcarousel_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
    
      $fieldset = $form->addFieldset('pcarousel_form', array('legend'=>Mage::helper('pcarousel')->__('Carousel Setting')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('pcarousel')->__('Name'),
          
          'name'      => 'title',
      ));

   $fieldset->addField('categories', 'select', array(
    'name' => 'categories',
    'label' => Mage::helper('pcarousel')->__('Categories'),
    'title' => Mage::helper('pcarousel')->__('Categories'),
    'required' => false,
    'values' => Mage::helper('pcarousel')->getAllCategoriesArray(true)
     ));
   
    $fieldset->addField('numberofproduct', 'text', array(
          'label'     => Mage::helper('pcarousel')->__('Number of Product'),
            'class'     => 'validate-number',
            'required' => true,
          'name'      => 'numberofproduct',
        
      ));
    
    $fieldset->addField('paginations', 'select', array(
          'label'     => Mage::helper('pcarousel')->__('Show Pagination'),
          'name'      => 'paginations',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('pcarousel')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('pcarousel')->__('No'),
              ),
          ),
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('pcarousel')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('pcarousel')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('pcarousel')->__('Disabled'),
              ),
          ),
      ));
     
     
     
      if ( Mage::getSingleton('adminhtml/session')->getInsuranceData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getInsuranceData());
          Mage::getSingleton('adminhtml/session')->setInsuranceData(null);
      } elseif ( Mage::registry('pcarousel_data') ) {
          $form->setValues(Mage::registry('pcarousel_data')->getData());
      }
      return parent::_prepareForm();
  }
}