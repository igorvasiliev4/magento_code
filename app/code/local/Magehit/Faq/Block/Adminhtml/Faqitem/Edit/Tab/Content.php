<?php

class Magehit_Faq_Block_Adminhtml_Faqitem_Edit_Tab_Content
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('faq/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {        

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('faqitem_');

        $fieldset = $form->addFieldset('content_fieldset', array('legend'=>Mage::helper('faq')->__('Content'),'class'=>'fieldset-wide'));

        $wysiwygConfig = Mage::getSingleton('faq/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );

        $fieldset->addField('question', 'editor', array(
            'name'      => 'question',
            'label'     => Mage::helper('cms')->__('Question'),
            'title'     => Mage::helper('cms')->__('Question'),
            'class'     => 'required-entry',
            'style'     => 'width:500px; height:80px;',
            'required'  => true,
        ));

        $contentField = $fieldset->addField('answer', 'editor', array(
           'name'      => 'answer',
          'label'     => Mage::helper('faq')->__('Answer'),
          'title'     => Mage::helper('faq')->__('Answer'),
          'class'     => 'required-entry',
          'style'     => 'width:500px; height:350px;',
          'required'  => true,
          'config'    => $wysiwygConfig
        ));


        //$form->setValues($model->getData());
        $this->setForm($form);
        
    	if ( Mage::getSingleton('adminhtml/session')->getFaqItemData() )
	      {
	          $form->setValues(Mage::getSingleton('adminhtml/session')->getFaqItemData());
	          Mage::getSingleton('adminhtml/session')->setFaqItemData(null);
	      } elseif ( Mage::registry('faqitem_data') ) {
	          $form->setValues(Mage::registry('faqitem_data')->getData());
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
        return Mage::helper('faq')->__('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('faq')->__('Content');
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
