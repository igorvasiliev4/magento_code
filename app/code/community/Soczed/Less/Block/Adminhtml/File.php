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

class Soczed_Less_Block_Adminhtml_File
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_file';
        $this->_blockGroup = 'less';
        $this->_headerText = $this->__('Manage Less Files');
        parent::__construct();
        $this->_removeButton('add');
    }
}