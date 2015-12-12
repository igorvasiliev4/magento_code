<?php
class Magehit_Faq_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$this->loadLayout();  
        
    	if ($head = $this->getLayout()->getBlock('head')) {
			$head->setTitle(Mage::helper('faq')->getPageTitle());
    		$head->setDescription(Mage::helper('faq')->getDescription());
            $head->setKeywords(Mage::helper('faq')->getKeyword());
            $head->setRobots(Mage::helper('faq')->getRobot());                                                 
        }
    	
		$this->renderLayout();
    }
}