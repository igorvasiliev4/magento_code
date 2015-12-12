<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Model_Widget_Source_Parallax_Image_Position{
    public function toOptionArray(){
        $types[] = array('value' => 'center',       'label' => 'center');
        $types[] = array('value' => 'left top',     'label' => 'left top');
        $types[] = array('value' => 'left center',  'label' => 'left center');
        $types[] = array('value' => 'left bottom',  'label' => 'left bottom');
        $types[] = array('value' => 'right top',    'label' => 'right top');
        $types[] = array('value' => 'right center', 'label' => 'right center');
        $types[] = array('value' => 'right bottom', 'label' => 'right bottom');
        $types[] = array('value' => 'center top',   'label' => 'center top');
        $types[] = array('value' => 'center right', 'label' => 'center right');
        $types[] = array('value' => 'center bottom', 'label' => 'center bottom');

        return $types;
    }
}