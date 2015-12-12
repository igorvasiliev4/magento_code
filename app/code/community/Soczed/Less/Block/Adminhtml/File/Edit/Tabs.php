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

class Soczed_Less_Block_Adminhtml_File_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('less_file_info_tabs');
        $this->setDestElementId('less_file_edit_form');
        $this->setTitle($this->__('Less File'));
    }
    
    public function getLessFile()
    {
        return Mage::registry('current_less_file');
    }
    
    protected function _prepareLayout()
    {
        $file = $this->getLessFile();
        
        $this->addTab('general', array(
            'label'   => $this->__('General'),
            'content' => $this->getLayout()->createBlock('less/adminhtml_file_edit_tab_general')->toHtml(),
            'active'  => true,
        ));
        
        return parent::_prepareLayout();
    }
}