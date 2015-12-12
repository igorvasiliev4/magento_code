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
class MagenThemes_MTGhost_Block_Adminhtml_Button_Restore_Restore extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $elementOriginalData = $element->getOriginalData();
        $buttonSuffix = '';
        if (isset($elementOriginalData['label']))
            $buttonSuffix = ' ' . $elementOriginalData['label'];
        $url = $this->getUrl('mtghost/adminhtml_restore/restore');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable restore')
            ->setLabel($buttonSuffix)
            ->setOnClick("setLocation('$url')")
            ->toHtml();
        return $html;
    }
}