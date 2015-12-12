<?php

class Magehit_Faq_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getPageTitle(){
    	return Mage::getStoreConfig('magehit_faq/general/page_title') ? Mage::getStoreConfig('magehit_faq/general/page_title'):"";
	}

	public function getKeyword(){
    	return Mage::getStoreConfig('magehit_faq/general/faq_keyword') ? Mage::getStoreConfig('magehit_faq/general/faq_keyword'):"";
	}
	
	public function getDescription(){
		return Mage::getStoreConfig('magehit_faq/general/faq_description') ? Mage::getStoreConfig('magehit_faq/general/faq_description'):"";
	}
	
	public function getRobot(){
		return Mage::getStoreConfig('magehit_faq/general/faq_robots') ? Mage::getStoreConfig('magehit_faq/general/faq_robots'):"INDEX,FOLLOW";
	}

	const MYNAME = "Magehit_Faq";
	function disableConfig()
	{
						
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,1);	
			Mage::getConfig()->reinit();
	}
	
	
}