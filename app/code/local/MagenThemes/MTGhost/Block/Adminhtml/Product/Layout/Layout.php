<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package Mato Framework
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       ZooExtension.com
 * @email        magento@cleversoft.co
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class MagenThemes_MTGhost_Block_Adminhtml_Product_Layout_Layout extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
        $html = parent::_getElementHtml($element);
        $imgParth = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/wysiwyg/magenthemes/mtghost/product/layout/';
        $js = $this->getJsUrl('magenthemes/jquery/jquery.min.js');
        $html .= '
                <script type="text/javascript" src="'. $js .'"></script>
                <script type="text/javascript">jQuery.noConflict();</script>
                ';
        $html .= '<br/><div id="layout_'.$element->getHtmlId().'" class="layout_preview" style="min-height: 210px;"></div>';
        $html .= '
            <script type="text/javascript">
                jQuery(window).load(function(){
                    var layout = jQuery("#'.$element->getHtmlId().' option:selected").val();
                    jQuery("#'.$element->getHtmlId().'")
                        .change(function() {
                            var imageLayout = "";
                            jQuery( "#'.$element->getHtmlId().' option:selected" ).each(function() {
                            imageLayout += "'.$imgParth.'"+jQuery( this ).val()+".png";
                        });
                        jQuery("#layout_'.$element->getHtmlId().'").html("<img src="+imageLayout+" />");
                    }).trigger("change");
                });
            </script>';
        return $html;
    }
}
?>