<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Attribute_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images{
    public function isUsingStaticUrlsAllowed(){
        if (Mage::getSingleton('adminhtml/session')->getStaticUrlsAllowed()){
            return true;
        }
        return parent::isUsingStaticUrlsAllowed();
    }
}