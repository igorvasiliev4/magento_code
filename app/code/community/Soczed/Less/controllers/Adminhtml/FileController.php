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

class Soczed_Less_Adminhtml_FileController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction($layoutIds=null)
    {
        $this->loadLayout($layoutIds)
            ->_setActiveMenu('magenthemes/less')
            ->_title(Mage::helper('less')->__('Manage Less Files'))
            ->_addBreadcrumb(Mage::helper('less')->__('Manage Less Files'), Mage::helper('less')->__('Manage Less Files'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    protected function _initLessFile($needId=false)
    {
        $fileId = (int) $this->getRequest()->getParam('id');
        $file   = Mage::getModel('less/file');

        if ($fileId) {
            $file->load($fileId);
        }
        if ($needId && !$file->getId()) {
            return false;
        }

        Mage::register('less_file', $file);
        Mage::register('current_less_file', $file);
        return $file;
    }

    public function editAction()
    {
        if (!$file = $this->_initLessFile(true)) {
            $this->_getSession()->addError($this->__('This Less file no longer exists.'));
            return $this->_redirect('*/*/');
        }

        $data = $this->_getSession()->getLessFileData(true);

        if (!empty($data)) {
            $file->addData($data);
        }

        $this->_initAction()
            ->_title($file->getPath())
            ->_addBreadcrumb($file->getPath())
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (!$file = $this->_initLessFile(true)) {
                $this->_getSession()->addError($this->__(' Less file no longer exists.'));
                return $this->_redirect('*/*/index');
            }

            if (isset($data['variables'])) {
                foreach ($data['variables'] as $key => $variable) {
                    if (isset($variable['delete'])) {
                        if ($variable['delete']) {
                            unset($data['variables'][$key]);
                        } else {
                            unset($data['variables'][$key]['delete']);
                        }
                    } elseif (empty($variable['code']) || empty($variable['value'])) {
                        unset($data['variables'][$key]);
                    }
                }
                $file->setCustomVariables($data['variables']);
                unset($data['variables']);
            }

            $file->addData($data);

            try {
                $file->save();

                $this->_getSession()->addSuccess($this->__('The Less file has been successfully saved.'));
                $this->_getSession()->setLessFileData(false);

                if ($redirectBack = $this->getRequest()->getParam('back', false)) {
                    return $this->_redirect('*/*/edit', array(
                        'id' => $file->getId(),
                        '_current' => true,
                    ));
                } else {
                    return $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setLessFileData($data);

                return $this->_redirect('*/*/edit', array(
                    'id' => $file->getId(),
                    '_current' => true,
                ));
            }
        }
        return $this->_redirect('*/*/', array('_current' => true));
    }

    public function resetAction()
    {
        if ($file = $this->_initLessFile(true)) {
            try {
                $file->setCache(null)->save();
                $this->_getSession()->addSuccess($this->__('The cache of the Less file has been successfully resetted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $file->getId()));
                return;
            }
        }
        $this->_getSession()->addError($this->__('This Less file no longer exists.'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($file = $this->_initLessFile(true)) {
            try {
                $file->delete();
                $this->_getSession()->addSuccess($this->__('The Less file has been successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $file->getId()));
                return;
            }
        }
        $this->_getSession()->addError($this->__('This Less file no longer exists.'));
        $this->_redirect('*/*/');
    }

    protected function _doMassAction($type)
    {
        if (!$this->_validateLessFiles()) {
            return false;
        }

        try {
            $filesIds   = $this->getRequest()->getParam('less_file');
            $filesCount = count($filesIds);

            foreach ($filesIds as $fileId) {
                $file = Mage::getModel('less/file')->load($fileId);

                switch ($type) {
                    case 'delete':
                        $file->delete();
                        break;
                    case 'reset':
                        $file->setCache(null)->save();
                        break;
                }
            }

            switch ($type) {
                case 'delete':
                    $this->_getSession()->addSuccess($this->__('Total of %d Less file(s) have been deleted.', $filesCount));
                    break;
                case 'reset':
                    $this->_getSession()->addSuccess($this->__('The cache of %d Less file(s) have been resetted.', $filesCount));
                    break;
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $e->getMessage();
        }

        return true;
    }

    public function massDeleteAction()
    {
        $this->_doMassAction('delete');
        $this->getResponse()->setRedirect($this->getUrl('*/*/index'));
    }

    public function massResetAction()
    {
        $this->_doMassAction('reset');
        $this->getResponse()->setRedirect($this->getUrl('*/*/index'));
    }

    protected function _validateLessFiles()
    {
        if (!is_array($this->getRequest()->getParam('less_file', null))) {
            $this->_getSession()->addError($this->__('Please select one or more Less files'));
            $this->_redirect('*/*/index', array('_current' => true));
            return false;
        }
        return true;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/less');
    }
}