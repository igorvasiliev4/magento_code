<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Model_Widget_Source_Parallax_Type{
    public function toOptionArray(){
        $types[] = array('value' => 'image', 'label' => Mage::helper('mtwidget')->__('Image'));
        $types[] = array('value' => 'video', 'label' => Mage::helper('mtwidget')->__('Video Service'));
        $types[] = array('value' => 'file', 'label' => Mage::helper('mtwidget')->__('Video File'));

        return $types;
    }
}
