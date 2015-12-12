<?php

class Magehit_Faq_Model_Faqitem extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/faqitem');
    }
 	public function validate()
    {
    	$_category= $_POST['category'];
        $errors = array();
        $helper = Mage::helper('faq');
        if($_category =='---Select one---'){
        	$errors[] = $helper->__('Faq Name is a required field');
        }
        if (!Zend_Validate::is($this->getQuestion(), 'NotEmpty')) {
            $errors[] = $helper->__('Question is a required field');
        }
    	if (!Zend_Validate::is($this->getCode(), 'NotEmpty')) {
            $errors[] = $helper->__('Security Code is a required field');
        }else {
        	if($this->getCode()!= $this->getCodetest()){
        		$errors[] = $helper->__('Security Code is incorrect');
        	}
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}