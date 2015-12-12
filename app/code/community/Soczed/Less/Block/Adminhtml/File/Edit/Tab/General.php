<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Soczed
 * @package    Soczed_Less
 * @copyright  Copyright (c) 2012 Soczed <magento@soczed.com> (Benoît Leulliette <benoit@soczed.com>)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Soczed_Less_Block_Adminhtml_File_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $file = Mage::registry('current_less_file');
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general', array('legend' => $this->__('General')));
        
        $yesNo = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        
        $fieldset->addField('force_rebuild', 'select', array(
            'name'   => 'force_rebuild',
            'label'  => $this->__('Force Rebuild'),
            'class'  => 'required-entry',
            'values' => $yesNo,
            'value'  => (bool)$file->getForceRebuild(),
        ));
        $fieldset->addField('use_global_variables', 'select', array(
            'name'   => 'use_global_variables',
            'label'  => $this->__('Use Global Variables'),
            'class'  => 'required-entry',
            'values' => $yesNo,
            'value'  => (bool)$file->getUseGlobalVariables(),
        ));
        $fieldset->addField('force_global_variables', 'select', array(
            'name'   => 'force_global_variables',
            'label'  => $this->__('Force Global Variables'),
            'class'  => 'required-entry',
            'values' => $yesNo,
            'value'  => (bool)$file->getForceGlobalVariables(),
        ));
        $fieldset->addField('variables', 'text', array(
            'name'  => 'variables',
            'label' => $this->__('Custom Variables List'),
            'class' => 'required-entry',
            'value' => $file->getCustomVariables(),
        ));
        $form->getElement('variables')->setRenderer(
            $this->getLayout()->createBlock('less/adminhtml_widget_form_element_variables')
        );
        
        $this->setForm($form);
        return $this;
    }
}