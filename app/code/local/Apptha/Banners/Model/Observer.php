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
* @package     Apptha_Banners
* @version     0.1.0
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
*
*/

class Apptha_Banners_Model_Observer 
{
	
	/**
     * Add the banner slider relation after the banner hase been added
     *
     * @param Varien_Event_Observer $observer observer object
     *
     * @return boolean
     */
    public function addBannerToSlider(Varien_Event_Observer $observer)
    {
    	$banner_id = $observer->getEvent()->getBanner()->getId();
    	$slideids = $observer->getEvent()->getSlide();
    	
    	Mage::getModel('banners/bannerSlide')->getCollection()->getValueByBanner($banner_id)->delete();
    	
    	foreach($slideids as $slide_id){
	    	$data = array(
	    		'slide_id'=>$slide_id,
	    		'banner_id'=>$banner_id
	    	);

	    	$banner_slide = Mage::getModel('banners/bannerSlide')
					->setData($data)
					->save();
	    	
    	}
    	return $this;
    }
}
