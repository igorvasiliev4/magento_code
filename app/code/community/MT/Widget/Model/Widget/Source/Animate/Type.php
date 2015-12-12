<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Model_Widget_Source_Animate_Type{
    public function toOptionArray(){
        return array(
            array('value' => 'effect-zoomOut', 'label' => Mage::helper('mtwidget')->__('Zoom Out')),
            array('value' => 'effect-zoomIn', 'label' => Mage::helper('mtwidget')->__('Zoom In')),
            //array('value' => 'effect-slideBottom', 'label' => Mage::helper('mtwidget')->__('Slide Bottom')),
            array('value' => 'effect-bounceIn', 'label' => Mage::helper('mtwidget')->__('Bounce In')),
            array('value' => 'effect-bounceInRight', 'label' => Mage::helper('mtwidget')->__('Bounce In Right')),
            //array('value' => 'effect-bounceInUp', 'label' => Mage::helper('mtwidget')->__('Bounce In Up')),
            //array('value' => 'effect-bounceInDown', 'label' => Mage::helper('mtwidget')->__('Bounce In Down')),
            array('value' => 'effect-pageTop', 'label' => Mage::helper('mtwidget')->__('Page Top')),
            //array('value' => 'effect-pageTopBack', 'label' => Mage::helper('mtwidget')->__('Page Top Back')),
            array('value' => 'effect-pageBottom', 'label' => Mage::helper('mtwidget')->__('Page Bottom')),
            array('value' => 'effect-starwars', 'label' => Mage::helper('mtwidget')->__('Star Wars')),
            array('value' => 'effect-pageLeft', 'label' => Mage::helper('mtwidget')->__('Page Left')),
            array('value' => 'effect-pageRight', 'label' => Mage::helper('mtwidget')->__('Page Right'))
        );
    }
}
