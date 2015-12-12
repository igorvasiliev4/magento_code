<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Widget_Block_Adminhtml_Widget_Renderer_Depend extends Mage_Adminhtml_Block_Template{
    public function _toHtml(){
        return "
<script type='text/javascript'>
Element.observe('{$this->getData('target')}', 'change', function(ev){
    var input = Event.element(ev);
    var me = $('{$this->getData('me')}');
    var url = '{$this->getData('url')}';
    if (input && me) window.updateAttributeOption(me, input, url, null);
});
window.updateAttributeOption = function(me, depend, url, value){
    if (value) var values = value.split(',');
    else var values = [];
    new Ajax.Request(url, {
        parameters: {value: $(depend).value},
        onSuccess: function(transport){
            try{
                var options = transport.responseText.evalJSON();
                $(me).update('');
                options.each(function(option){
                    var optionElm = new Element('option', {value:option.id});
                    if (values.indexOf(option.id) !== -1){
                        optionElm.writeAttribute('selected', 'selected');
                    }
                    optionElm.update(option.label);
                    $(me).appendChild(optionElm);
                });
            }catch(e){}
        }
    });
}
window.updateAttributeOption('{$this->getData('me')}', '{$this->getData('target')}', '{$this->getData('url')}', '{$this->getData('value')}');
</script>";
    }
}