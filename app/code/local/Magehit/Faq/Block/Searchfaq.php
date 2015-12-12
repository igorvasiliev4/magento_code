<?php
class Magehit_Faq_Block_Searchfaq extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFaqitem()     
     {
        if (!$this->hasData('faqitem')) {
            $this->setData('faqitem', Mage::registry('faqitem'));
        }
        return $this->getData('faqitem');
        
    }  
}