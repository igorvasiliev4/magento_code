<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package      MT_AjaxCart
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
class MagenThemes_AjaxCart_Block_About_Information extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {		
	$html = $this->_getHeaderHtml($element);		
	$html.= $this->_getFieldHtml($element);        
        $html .= $this->_getFooterHtml($element);
        return $html;
    }   
    protected function _getFieldHtml($fieldset)
    {
	$content = 'Ajax Cart version : 2.0.0<br/>Author : <a href="http://www.9magentothemes.com" title="Magento Themes">9MagentoThemes.Com</a><br />Copyright &copy; 2011- 9MagentoThemes.Com';
	return $content;
    }
}