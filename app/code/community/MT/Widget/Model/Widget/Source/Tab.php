<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Model_Widget_Source_Tab{
    public function toOptionArray(){
        return array(
            array('value' => 'none', 'label' => Mage::helper('mtwidget')->__('None')),
            array('value' => 'categories', 'label' => Mage::helper('mtwidget')->__('Categories')),
            array('value' => 'collections', 'label' => Mage::helper('mtwidget')->__('Collections'))
        );
    }
}
