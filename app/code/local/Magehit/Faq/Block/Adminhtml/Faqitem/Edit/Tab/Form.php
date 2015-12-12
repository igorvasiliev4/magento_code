<?php
class Magehit_Faq_Block_Adminhtml_Faqitem_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	
	protected function _prepareForm()
	{
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('faqitem_');

	       $fieldset = $form->addFieldset('faqitem_form', array('legend'=>Mage::helper('faq')->__('FAQ Item information')));
		   
	      $faqs = array(''=>'-- Select FAQ Category --');
		  $collection = Mage::getModel('faq/faq')->getCollection();
		  foreach ($collection as $faq) {
			 $faqs[$faq->getId()] = $faq->getFaqname();
		  }
	
		  $fieldset->addField('faq_id', 'select', array(
	          'label'     => Mage::helper('faq')->__('FAQ Category Name'),
	          'name'      => 'faq_id',
		      'class'     => 'required-entry',
	          'required'  => true,
	          'values'    => $faqs,
	      ));
	      
		 $fieldset->addField('faq_order', 'text', array(
	          'label'     => Mage::helper('faq')->__('Order'),
	          'class'     => 'required-entry',
	          'required'  => true,
	          'name'      => 'faq_order',
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
          
	      $this->setForm($form);
	      
	      if ( Mage::getSingleton('adminhtml/session')->getFaqItemData() )
	      {
	          $form->setValues(Mage::getSingleton('adminhtml/session')->getFaqItemData());
	          Mage::getSingleton('adminhtml/session')->setFaqItemData(null);
	      } elseif ( Mage::registry('faqitem_data') ) {
	          $form->setValues(Mage::registry('faqitem_data')->getData());
	      	  if($faq=Mage::registry('faqitem_data')->getId()){
	      	  		if (!Mage::app()->isSingleStoreMode()) {
					$collection =  Mage::getModel('faq/faqitem')->getCollection();
					$collection->join('faqitem_store', 'faqitem_store.faqitem_id = main_table.faqitem_id AND main_table.faqitem_id='.$faq, 'faqitem_store.store_id');
					
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
  
     /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('faq')->__('General');
    }
    
     /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('faq')->__('General');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}