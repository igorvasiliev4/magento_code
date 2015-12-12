<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Model_Widget_Source_Type{
    public function toOptionArray(){
        $types = array(
            array('value' => 'product', 'label' => Mage::helper('mtwidget')->__('Product')),
            array('value' => 'block', 'label' => Mage::helper('mtwidget')->__('Block')),
            array('value' => 'attribute', 'label' => Mage::helper('mtwidget')->__('Attribute')),
            array('value' => 'category', 'label' => Mage::helper('mtwidget')->__('Category')),
            array('value' => 'blog', 'label' => Mage::helper('mtwidget')->__('Blog'))
        );

        return $types;
    }
}
