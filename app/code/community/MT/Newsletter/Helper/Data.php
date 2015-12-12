<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package      MT_Newsletter
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       ZooExtension.com
 * @email        magento@cleversoft.co
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class MT_Newsletter_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCfgEnable()
    {
        return Mage::getStoreConfig('mtnewsletter/display_options/enable');
    }
    public function getCfgWidth()
    {
        return Mage::getStoreConfig('mtnewsletter/display_options/width');
    }
    public function getCfgHeight()
    {
        return Mage::getStoreConfig('mtnewsletter/display_options/height');
    }
    public function getCfgBackgroundColor()
    {
        return Mage::getStoreConfig('mtnewsletter/display_options/background_color');
    }
    public function getCfgBackgroundImage()
    {
        return Mage::getStoreConfig('mtnewsletter/display_options/background_image');
    }
    public function getCfgIntro()
    {
        return Mage::getStoreConfig('mtnewsletter/display_options/intro');
    }
}