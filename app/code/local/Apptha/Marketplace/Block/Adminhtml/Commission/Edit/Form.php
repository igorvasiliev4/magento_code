<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.7
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
/**
 * Get Comments from admin
 * Form to get the comments from admin after transfer the payment to seller
 */
class Apptha_Marketplace_Block_Adminhtml_Commission_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{

    /**
     * Get the comments from admin after transfer the payment to seller
     * 
     * @return void
     */
 protected function _prepareForm(){
  
    $seller_id  = $this->getRequest()->getParam('id');   
    $collection = Mage::getModel('marketplace/transaction')            
                  ->load($seller_id,'seller_id');               
    $this->setCollection($collection);            
    $form       = new Varien_Data_Form(array(
                  'id'      => 'edit_form',
                  'action'  => $this->getUrl('*/*/pay', array('id' => $this->getRequest()->getParam('id'))),
                  'method'  => 'post',
                  'enctype' => 'multipart/form-data'
        )
     );
     $fieldset = $form->addFieldset('add_comment', array('legend' => Mage::helper('marketplace')->__('Comments')));
     $fieldset->addField('detail', 'textarea', array(
            'name'      => 'detail',
            'title'     => Mage::helper('marketplace')->__('Comments'),
            'label'     => Mage::helper('marketplace')->__('Comments'),
            'style'     => 'height: 200px;',
            'required'  => true,
            'value'     => $collection['comment']
        ));     
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
