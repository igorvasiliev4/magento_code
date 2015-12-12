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

class Soczed_Less_Block_Adminhtml_Widget_Form_Element_Variables
    extends Mage_Adminhtml_Block_Widget_Form
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element;
    
    public function __construct()
    {
        $this->setTemplate('soczed/less/widget/form/element/variables.phtml');
    }
    
    public function getLessFile()
    {
        return Mage::registry('current_less_file');
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }
    
    public function getElement()
    {
        return $this->_element;
    }
    
    public function getValues()
    {
        $values = array();
        $data   = $this->getElement()->getValue();
        
        if (is_array($data)) {
            usort($data, array($this, '_sortValues'));
            $values = $data;
        }
        
        return $values;
    }
    
    protected function _sortValues($a, $b)
    {
        $result = strcmp($a['code'], $b['code']);
        return ($result === 0 ? strcasecmp($a['value'], $b['value']) : $result);
    }
    
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => $this->__('Add Variable'),
                'onclick' => 'return variablesSourceControl.addItem()',
                'class'   => 'add',
            ));
        $button->setName('add_variable_item_button');
        
        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
    
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}