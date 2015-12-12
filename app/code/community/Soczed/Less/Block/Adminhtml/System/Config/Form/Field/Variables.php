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

class Soczed_Less_Block_Adminhtml_System_Config_Form_Field_Variables
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
     public function __construct()
    {
        $this->addColumn('code', array(
            'label' => $this->__('Code'),
            'style' => 'width:110px;',
        ));
        $this->addColumn('value', array(
            'label' => $this->__('Value'),
            'style' => 'width:110px;',
        ));
        
        $this->_addAfter = false;
        $this->_addButtonLabel = $this->__('Add Variable');
        parent::__construct();
    }
}