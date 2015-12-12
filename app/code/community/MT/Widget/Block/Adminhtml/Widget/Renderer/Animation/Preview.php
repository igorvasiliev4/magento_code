<?php
/**
 * @category    MT
 * @package     MT_Widget
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Widget_Block_Adminhtml_Widget_Renderer_Animation_Preview
    extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface{

    protected $_element;

    public function __construct(){
        parent::__construct();
        $this->setTemplate('mt/widget/animation/preview.phtml');
    }

    public function render(Varien_Data_Form_Element_Abstract $element){
        $this->setElement($element);
        return $this->toHtml();
    }

    public function setElement($element){
        $this->_element = $element;
    }

    public function getElement(){
        return $this->_element;
    }
}
