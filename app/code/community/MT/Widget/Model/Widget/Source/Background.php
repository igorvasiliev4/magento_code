<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Model_Widget_Source_Background{
    public function toOptionArray(){
        return array(
            array('value' => '', 'label' => 'No'),
            array('value' => 'parallax', 'label' => 'Parallax'),
            array('value' => 'kenburns', 'label' => 'Ken Burns')
        );
    }
}
