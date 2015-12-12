<?php
/**
 * @category    MT
 * @package     MT_Extensions
 * Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

class MT_Extensions_Block_Adminhtml_Cms_Wysiwyg_Images_Content extends Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content{
    public function getFilebrowserSetupObject(){
        $setupObject = new Varien_Object();

        $setupObject->setData(array(
            'newFolderPrompt'                   => $this->helper('cms')->__('New Folder Name:'),
            'deleteFolderConfirmationMessage'   => $this->helper('cms')->__('Are you sure you want to delete current folder?'),
            'deleteFileConfirmationMessage'     => $this->helper('cms')->__('Are you sure you want to delete the selected file?'),
            'targetElementId'                   => $this->getTargetElementId(),
            'contentsUrl'                       => $this->getContentsUrl(),
            'onInsertUrl'                       => $this->getOnInsertUrl(),
            'newFolderUrl'                      => $this->getNewfolderUrl(),
            'deleteFolderUrl'                   => $this->getDeletefolderUrl(),
            'deleteFilesUrl'                    => $this->getDeleteFilesUrl(),
            'headerText'                        => $this->getHeaderText(),
            'onInsertCallback'                  => $this->getOnInsertCallback(),
            'onInsertCallbackParams'            => $this->getOnInsertCallbackParams(),
            'windowId'                          => $this->getWindowId()
        ));

        return Mage::helper('core')->jsonEncode($setupObject);
    }

    public function getOnInsertCallback(){
        return $this->getRequest()->getParam('onInsertCallback');
    }

    public function getOnInsertCallbackParams(){
        return $this->getRequest()->getParam('onInsertCallbackParams');
    }

    public function getWindowId(){
        return $this->getRequest()->getParam('windowId');
    }
}
