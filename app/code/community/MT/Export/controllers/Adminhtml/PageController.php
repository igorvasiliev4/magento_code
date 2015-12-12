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
class MT_Export_Adminhtml_PageController extends Mage_Adminhtml_Controller_Action
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
            ->_title(Mage::helper('export')->__('Manage Export Page'))
            ->_addBreadcrumb(Mage::helper('export')->__('Manage Export Page'), Mage::helper('export')->__('Manage Export Page'));
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
        $fileName   = 'pages.xml';
        $pageIds = $this->getRequest()->getParam('page_ids');
        if(!is_array($pageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $collection = Mage::getModel('cms/page')->getCollection()->addFieldToFilter('page_id',array('in'=>$pageIds));
                $xml = '<root>';
                $xml.= '<pages>';
                foreach ($collection as $page) {
                    $xml.= '<cms_block>';
                    $xml.= '<title>'.$page->getTitle().'</title>';
                    $xml.= '<identifier>'.$page->getIdentifier().'</identifier>';
                    $xml.= '<content><![CDATA['.$page->getContent().']]></content>';
                    $xml.= '<is_active>'.$page->getIsActive().'</is_active>';
                    $xml.= '<stores><item>0</item></stores>';
                    $xml.= '</cms_block>';
                }
                $xml.= '</pages>';
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