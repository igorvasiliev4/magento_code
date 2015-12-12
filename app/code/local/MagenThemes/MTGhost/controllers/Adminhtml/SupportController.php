<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package Mato Framework
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

class MagenThemes_MTGhost_Adminhtml_SupportController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('magenthemes/mtghost')
            ->_title(Mage::helper('adminhtml')->__('Help & Support'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Help & Support'), Mage::helper('adminhtml')->__('Help & Support'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }
} 
?>
