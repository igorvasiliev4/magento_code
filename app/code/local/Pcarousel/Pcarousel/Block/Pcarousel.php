<?php
class Pcarousel_Pcarousel_Block_Pcarousel extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getInsurance()     
     { 
        if (!$this->hasData('pcarousel')) {
            $this->setData('pcarousel', Mage::registry('pcarousel'));
        }
        return $this->getData('pcarousel');
        
    }
}