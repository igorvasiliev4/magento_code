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
class MT_Export_Adminhtml_WidgetController extends Mage_Adminhtml_Controller_Action
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
            ->_title(Mage::helper('export')->__('Manage Export Widget'))
            ->_addBreadcrumb(Mage::helper('export')->__('Manage Export Widget'), Mage::helper('export')->__('Manage Export Widget'));
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
        $fileName   = 'widgets.xml';
        $instanceIds = $this->getRequest()->getParam('instance_ids');
        if(!is_array($instanceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $collection = Mage::getModel('widget/widget_instance')->getCollection()->addFieldToFilter('instance_id',array('in'=>$instanceIds));
                $xml = '<root>';
                    $xml.= '<widgets>';
                    foreach ($collection as $instance) {
                        $xml.= '<instance>';
                            $xml.= '<title>'.$instance->getTitle().'</title>';
                            $xml.= '<instance_type>'.$instance->getInstanceType().'</instance_type>';
                            $xml.= '<package_theme>'.$instance->getPackageTheme().'</package_theme>';
                            $xml.= '<widget_parameters><![CDATA['.serialize($instance->getWidgetParameters()).']]></widget_parameters>';
                            $xml.= '<sort_order>'.$instance->getSortOrder().'</sort_order>';
                            $pages = Mage::getSingleton('export/resource_widget_instance')->pageItemsLoad($instance->getInstanceId());
                            $packageTheme = explode('/', $instance->getPackageTheme());
                            foreach($pages as $page){
                                $xml.='<page>';
                                    $xml.='<group>'.$page['page_group'].'</group>';
                                    $xml.='<layout_handle>'.$page['layout_handle'].'</layout_handle>';
                                    $xml.='<block_reference>'.$page['block_reference'].'</block_reference>';
                                    $xml.='<for>'.$page['page_for'].'</for>';
                                    $xml.='<entities>'.$page['entities'].'</entities>';
                                    $xml.='<template>'.$page['page_template'].'</template>';
                                    $xml.='<area>frontend</area>';
                                    $xml.='<package>'.$packageTheme[0].'</package>';
                                    $xml.='<theme>'.$packageTheme[1].'</theme>';
                                $xml.='</page>';
                            }
                            $layoutUpdate = Mage::getSingleton('export/resource_widget_instance')->layoutLoad($instance->getInstanceId());
                            $xml.='<xml><![CDATA['.$layoutUpdate.']]></xml>';
                        $xml.= '</instance>';
                    }
                    $xml.= '</widgets>';
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