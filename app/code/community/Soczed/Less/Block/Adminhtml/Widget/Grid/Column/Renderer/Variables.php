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

class Soczed_Less_Block_Adminhtml_Widget_Grid_Column_Renderer_Variables
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected function _sortValues($a, $b)
    {
        $result = strcmp($a['code'], $b['code']);
        return ($result === 0 ? strcasecmp($a['value'], $b['value']) : $result);
    }
    
    protected function _getValue(Varien_Object $row)
    {
        if (is_array($value = parent::_getValue($row))) {
            usort($value, array($this, '_sortValues'));
            $html = array();
            
            foreach ($value as $variable) {
                $html[] = '<strong>'.$this->htmlEscape($variable['code']).'</strong>'
                    . ' : ' . $this->htmlEscape($variable['value']);
            }
            
            return implode('<br />', $html);
        } else {
            return $this->__('None');
        }
    }
}