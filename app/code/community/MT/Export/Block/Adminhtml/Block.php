<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     Mato
 * @package      MT_Export
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       ZooExtension.com
 * @email        magento@cleversoft.co
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class MT_Export_Block_Adminhtml_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_block';
        $this->_blockGroup = 'export';
        $this->_headerText = $this->__('Manage Export Static Block');
        parent::__construct();
        $this->_removeButton('add');
    }
}