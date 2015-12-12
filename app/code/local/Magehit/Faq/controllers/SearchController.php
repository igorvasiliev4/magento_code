<?php
class Magehit_Faq_SearchController extends Mage_Core_Controller_Front_Action
{
	/*public function indexAction()
	{
		$this->loadLayout();     
		$this->renderLayout();
	}*/
	public function searchAction() 
	{
		$this->loadLayout(); 
		if ($head = $this->getLayout()->getBlock('head')) {
    		$head->setDescription(Mage::helper('faq')->getDescription());
            $head->setKeywords(Mage::helper('faq')->getKeyword());
            $head->setRobots(Mage::helper('faq')->getRobot());                                                 
        }    
		$this->renderLayout();
	}
}