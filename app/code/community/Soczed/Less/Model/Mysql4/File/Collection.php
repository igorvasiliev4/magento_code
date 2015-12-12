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

class Soczed_Less_Model_Mysql4_File_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('less/file');
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->walk('afterLoad');
        return $this;
    }
    
    public function toOptionArray()
    {
        return parent::_toOptionArray('file_id', 'path');
    }
    
    public function toOptionHash()
    {
        return parent::_toOptionHash('file_id', 'path');
    }
    
    public function toPathCacheOptionArray()
    {
        return parent::_toOptionArray('path', 'cache');
    }
    
    public function toPathCacheOptionHash()
    {
        return parent::_toOptionHash('path', 'cache');
    }
}