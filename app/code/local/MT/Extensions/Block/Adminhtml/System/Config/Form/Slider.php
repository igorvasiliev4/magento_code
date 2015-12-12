<?php
/**
 * @category    MT
 * @package     MT_Extensions
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Extensions_Block_Adminhtml_System_Config_Form_Slider extends Mage_Adminhtml_Block_System_Config_Form_Field{
    protected $element;

    protected function _prepareLayout(){
        $this->setTemplate('mtext/adminhtml/system/config/form/slider.phtml');
        return $this;
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->element = $element;
        return $this->toHtml();
    }

    public function getElement(){
        $form = $this->getForm()->getForm();
        foreach ($form->getElements() as $fieldset){
            $fieldsetId = $fieldset->getId();
            $group = $fieldset->getGroup();
            foreach ($group->fields as $elements){
                foreach ($elements as $id => $element){
                    if ($fieldsetId.'_'.$id == $this->element->getId()){
                        $this->element->setRange($element->range);
                    }
                }
            }
        }
        return $this->element;
    }
}
