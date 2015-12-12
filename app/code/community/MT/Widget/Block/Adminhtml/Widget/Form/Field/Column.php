<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Widget_Block_Adminhtml_Widget_Form_Field_Column extends Mage_Adminhtml_Block_Template
{
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element){
        $html = '<script type="text/javascript">' .
            'function showHideColumn(){ if(jQuery("select[name=\'parameters[widget_type]\']").val() == \'block\' && jQuery("select[name=\'parameters[scroll]\']").val() == 0) jQuery("input[name=\'parameters[column]\']").closest( "tr").hide(); else jQuery("input[name=\'parameters[column]\']").closest( "tr").show();}</script>';
        $element->setOnchange('showHideColumn();');
        $element->setData('after_element_html', $html);
        return $element;
    }
}