<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Widget_Block_Adminhtml_Widget_Renderer_Layout extends Mage_Adminhtml_Block_Template{
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element){
        $html = "
<div id='layout_{$this->getData("target")}' class='layout-preview'></div>
<script type='text/javascript'>
    document.observe('dom:loaded', function(){
        window.layout_{$this->getData("target")}_object = new MTLayoutPreview('layout_{$this->getData("target")}', '{$this->getFieldsetId()}', '{$this->getData("target")}');
    });
    setTimeout(function(){
        window.layout_{$this->getData("target")}_object = new MTLayoutPreview('layout_{$this->getData("target")}', '{$this->getFieldsetId()}', '{$this->getData("target")}');
    }, 100);
</script>
";
        $element->setData('after_element_html', $html);
        return $element;
    }
}