<?php
class Magehit_Faq_SubmitController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		if ($head = $this->getLayout()->getBlock('head')) {
    		$head->setDescription(Mage::helper('faq')->getDescription());
            $head->setKeywords(Mage::helper('faq')->getKeyword());
            $head->setRobots(Mage::helper('faq')->getRobot());                                                 
        }
		$this->renderLayout();
	}
	public function saveAction() {
		
		$data = $this->getRequest()->getPost();
		$_category= $_POST['category'];
        if (!empty($data)) {
            $session = Mage::getSingleton('core/session', array('name'=>'frontend'));
            /* @var $session Mage_Core_Model_Session */
            $faqitem     = Mage::getModel('faq/faqitem')->setData($data);
            /* @var $review Mage_Review_Model_Review */

            $validate = $faqitem->validate();
            if ($validate === true) {
                try {                    
                    $faqs= Mage::getModel('faq/faq')->getCollection()
						->addFieldToFilter('faqname',$_category);
					$data = $faqs->getData();
					
					$_faqid= $data[0]['faq_id'];
					$faqitem->setFaq_id($_faqid);
					$faqitem->setCreatedTime(now());
					$faqitem->setUpdateTime(now());
                	
					$faqitem->save();
					
                    $session->addSuccess($this->__('Your question has been accepted'));
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $session->addError($this->__('Unable to post question. Please, try again later.'));
                }
            }
            else {
                try{
                $session->setFormData($data);
                }
                catch(Exception $e){
                    Mage::log($e->getMessage());
                }                  
                if (is_array($validate)) {                   
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }                 
                }
                else {
                    $session->addError($this->__('Unable to post question. Please, try again later.'));
                }
            }
        }

        if ($redirectUrl = Mage::getSingleton('core/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();		
	}
}