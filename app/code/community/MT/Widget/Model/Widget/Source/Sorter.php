<?php

class MT_Widget_Model_Widget_Source_Sorter
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Varien_Data_Collection::SORT_ORDER_DESC,
                'label' => Mage::helper('mtwidget')->__('Newest first'),
            ),
            array(
                'value' => MT_Widget_Helper_Data::BLOG_POST_ORDER_RANDOM,
                'label' => Mage::helper('mtwidget')->__('Random'),
            ),
        );
    }
}