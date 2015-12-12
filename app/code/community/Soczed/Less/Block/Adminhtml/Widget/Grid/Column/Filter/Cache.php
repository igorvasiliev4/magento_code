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

class Soczed_Less_Block_Adminhtml_Widget_Grid_Column_Filter_Cache
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        return array(
            array(
                'value' => null,
                'label' => null,
            ),
            array(
                'value' => 1,
                'label' => $this->__('Existing'),
            ),
            array(
                'value' => 0,
                'label' => $this->__('None'),
            ),
        );
    }
    
    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }
        return ((bool)$this->getValue() ? array('notnull' => true) : array('null' => true));
    }
}