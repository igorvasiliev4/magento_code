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

class Soczed_Less_Model_File
    extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('less/file');
        $this->setIdFieldName('file_id');
    }
    
    protected function _afterLoad()
    {
        if (is_array($cache = @unserialize($this->getData('cache')))) {
            $this->setData('cache', $cache);
        } else {
            $this->setData('cache', null);
        }
        if (is_array($variables = @unserialize($this->getData('custom_variables')))) {
            $this->setData('custom_variables', $variables);
        } else {
            $this->setData('custom_variables', null);
        }
        return parent::_afterLoad();
    }
    
    protected function _beforeSave()
    {
        if (is_array($this->getData('cache'))) {
            $this->setData('cache', @serialize($this->getData('cache')));
        } else {
            $this->setData('cache', null);
        }
        if (is_array($this->getData('custom_variables'))) {
            $this->setData('custom_variables', @serialize($this->getData('custom_variables')));
        } else {
            $this->setData('custom_variables', null);
        }
        return parent::_beforeSave();
    }
    
    protected function _afterSave()
    {
        if (is_array($cache = @unserialize($this->getData('cache')))) {
            $this->setData('cache', $cache);
        } else {
            $this->setData('cache', null);
        }
        if (is_array($variables = @unserialize($this->getData('custom_variables')))) {
            $this->setData('custom_variables', $variables);
        } else {
            $this->setData('custom_variables', null);
        }
        return parent::_afterSave();
    }
}