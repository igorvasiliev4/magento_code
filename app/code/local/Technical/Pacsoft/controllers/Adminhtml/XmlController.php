<?php
class Technical_Pacsoft_Adminhtml_XmlController extends Mage_Adminhtml_Controller_Action
{
    public function pprivatAction()
    {
        if ($this->getRequest()->isPost()) {
            $ids = $this->getRequest()->getPost('order_ids');
            $file = Mage::getModel('pdpacsoft/xml')->getPdpFile('pprivat', $ids);
            $helper = Mage::helper('pdpacsoft');

            if ($file) {
                $result = $helper->sendOrder($file);
                if ($result === true) {
                    $link = $helper->getLoginLink();
                    $msg = $helper->__('Created. <a target="_blank" href="%s">Link on orders.</a>', $link);
                    $this->_getSession()->addSuccess($msg);
                } else {
                     $msg = $helper->__($result);
                     $this->_getSession()->addError($msg);
                }
            } else {
                $msg = $helper->__('Error: Please set Quick ID in extension configurations!');
                $this->_getSession()->addError($msg);
            }
            $this->_redirect('adminhtml/sales_order/');
        }
    }

    public function oprivatAction()
    {
        if ($this->getRequest()->isPost()) {
            $ids = $this->getRequest()->getPost('order_ids');
            $file = Mage::getModel('pdpacsoft/xml')->getPdpFile('oprivat', $ids);
            $helper = Mage::helper('pdpacsoft');

            if ($file) {
                $result = $helper->sendOrder($file);
                if ($result === true) {
                    $link = $helper->getLoginLink();
                    $msg = $helper->__('Created. <a target="_blank" href="%s">Link on orders.</a>', $link);
                    $this->_getSession()->addSuccess($msg);
                } else {
                     $msg = $helper->__($result);
                     $this->_getSession()->addError($msg);
                }
            } else {
                $msg = $helper->__('Error: Please set Quick ID in extension configurations!');
                $this->_getSession()->addError($msg);
            }
            $this->_redirect('adminhtml/sales_order/');
        }
    }

    public function preturAction()
    {
        if ($this->getRequest()->isPost()) {
            $ids = $this->getRequest()->getPost('order_ids');
            $file = Mage::getModel('pdpacsoft/xml')->getPdpFile('pretur', $ids);
            $helper = Mage::helper('pdpacsoft');

            if ($file) {
                $result = $helper->sendOrder($file);
                if ($result === true) {
                    $link = $helper->getLoginLink();
                    $msg = $helper->__('Created. <a target="_blank" href="%s">Link on orders.</a>', $link);
                    $this->_getSession()->addSuccess($msg);
                } else {
                     $msg = $helper->__($result);
                     $this->_getSession()->addError($msg);
                }
            } else {
                $msg = $helper->__('Error: Please set Quick ID in extension configurations!');
                $this->_getSession()->addError($msg);
            }
            $this->_redirect('adminhtml/sales_order/');
        }
    }

    public function eerhvervAction()
    {
        if ($this->getRequest()->isPost()) {
            $ids = $this->getRequest()->getPost('order_ids');
            $file = Mage::getModel('pdpacsoft/xml')->getPdpFile('eerhverv', $ids);
            $helper = Mage::helper('pdpacsoft');

            if ($file) {
                $result = $helper->sendOrder($file);
                if ($result === true) {
                    $link = $helper->getLoginLink();
                    $msg = $helper->__('Created. <a target="_blank" href="%s">Link on orders.</a>', $link);
                    $this->_getSession()->addSuccess($msg);
                } else {
                     $msg = $helper->__($result);
                     $this->_getSession()->addError($msg);
                }
            } else {
                $msg = $helper->__('Error: Please set Quick ID in extension configurations!');
                $this->_getSession()->addError($msg);
            }
            $this->_redirect('adminhtml/sales_order/');
        }
    }

    public function ereturAction()
    {
        if ($this->getRequest()->isPost()) {
            $ids = $this->getRequest()->getPost('order_ids');
            $file = Mage::getModel('pdpacsoft/xml')->getPdpFile('eretur', $ids);
            $helper = Mage::helper('pdpacsoft');

            if ($file) {
                $result = $helper->sendOrder($file);
                if ($result === true) {
                    $link = $helper->getLoginLink();
                    $msg = $helper->__('Created. <a target="_blank" href="%s">Link on orders.</a>', $link);
                    $this->_getSession()->addSuccess($msg);
                } else {
                     $msg = $helper->__($result);
                     $this->_getSession()->addError($msg);
                }
            } else {
                $msg = $helper->__('Error: Please set Quick ID in extension configurations!');
                $this->_getSession()->addError($msg);
            }
            $this->_redirect('adminhtml/sales_order/');
        }
    }

    public function pprivatvAction()
    {
        if ($this->getRequest()->isPost()) {
            $ids = $this->getRequest()->getPost('order_ids');
            $file = Mage::getModel('pdpacsoft/xml')->getPdpFile('pprivatv', $ids);
            $helper = Mage::helper('pdpacsoft');

            if ($file) {
                $result = $helper->sendOrder($file);
                if ($result === true) {
                    $link = $helper->getLoginLink();
                    $msg = $helper->__('Created. <a target="_blank" href="%s">Link on orders.</a>', $link);
                    $this->_getSession()->addSuccess($msg);
                } else {
                     $msg = $helper->__($result);
                     $this->_getSession()->addError($msg);
                }
            } else {
                $msg = $helper->__('Error: Please set Quick ID in extension configurations!');
                $this->_getSession()->addError($msg);
            }
            $this->_redirect('adminhtml/sales_order/');
        }
    }
}