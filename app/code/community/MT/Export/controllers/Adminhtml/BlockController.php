<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
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
class MT_Export_Adminhtml_BlockController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_PageController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('mt/export')
            ->_title(Mage::helper('export')->__('Manage Export Static Block'))
            ->_addBreadcrumb(Mage::helper('export')->__('Manage Export Static Block'), Mage::helper('export')->__('Manage Export Static Block'));
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

    /**
     *  Export page grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'blocks.xml';
        $blockIds = $this->getRequest()->getParam('block_ids');
        if(!is_array($blockIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $collection = Mage::getModel('cms/block')->getCollection()->addFieldToFilter('block_id',array('in'=>$blockIds));
                $xml = '<root>';
                    $xml.= '<blocks>';
                    foreach ($collection as $block) {
                        $xml.= '<cms_block>';
                        $xml.= '<title>'.$block->getTitle().'</title>';
                        $xml.= '<identifier>'.$block->getIdentifier().'</identifier>';
                        $xml.= '<content><![CDATA['.$block->getContent().']]></content>';
                        $xml.= '<is_active>'.$block->getIsActive().'</is_active>';
                        $xml.= '<stores><item>0</item></stores>';
                        $xml.= '</cms_block>';
                    }
                    $xml.= '</blocks>';
                $xml.= '</root>';
                $this->_sendUploadResponse($fileName, $xml);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}