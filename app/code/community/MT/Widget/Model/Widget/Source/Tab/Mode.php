<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Model_Widget_Source_Tab_Mode{
    public function toOptionArray(){
        return array(
            array('value' => 'latest', 'label' => Mage::helper('mtwidget')->__('Latest Products')),
            array('value' => 'new', 'label' => Mage::helper('mtwidget')->__('New Products')),
            array('value' => 'bestsell', 'label' => Mage::helper('mtwidget')->__('Best Sell Products')),
            array('value' => 'mostviewed', 'label' => Mage::helper('mtwidget')->__('Most Viewed Products')),
            //array('value' => 'specificed', 'label' => Mage::helper('mtwidget')->__('Specified Products')),
            array('value' => 'random', 'label' => Mage::helper('mtwidget')->__('Random Products')),
            //array('value' => 'related', 'label' => Mage::helper('mtwidget')->__('Related Products')),
            //array('value' => 'up', 'label' => Mage::helper('mtwidget')->__('Up-sell Products')),
            //array('value' => 'cross', 'label' => Mage::helper('mtwidget')->__('Cross-sell Products')),
            array('value' => 'discount', 'label' => Mage::helper('mtwidget')->__('Discount Products')),
            array('value' => 'rating', 'label' => Mage::helper('mtwidget')->__('Top Rated Products'))
        );
    }
}
