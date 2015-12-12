<?php
class Magehit_Faq_Block_Faq extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFaq()     
     { 
        if (!$this->hasData('faq')) {
            $this->setData('faq', Mage::registry('faq'));
        }
        return $this->getData('faq');
        
    }
    public function getContent($content){
		$version = Mage::getVersion();
        if(version_compare($version, '1.4.0.1', '>=')===true){
	        // Generate html from code block if exist
	        $helper = Mage::helper('cms');
	        $processor = $helper->getPageTemplateProcessor();
	        $html = $processor->filter($content);
	        $html = $this->getMessagesBlock()->getGroupedHtml() . $html;
	        return $html;
        }
        return $content;
    }
	        
}