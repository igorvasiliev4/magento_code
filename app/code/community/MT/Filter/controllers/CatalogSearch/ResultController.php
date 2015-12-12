<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

include_once 'Mage/CatalogSearch/controllers/ResultController.php';
class MT_Filter_CatalogSearch_ResultController extends Mage_CatalogSearch_ResultController {
    /**
     * Display search result
     *
     */
    public function indexAction() {
        $query = Mage::helper('catalogsearch')->getQuery();
        /* @var $query Mage_CatalogSearch_Model_Query */

        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                } else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()){
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                } else {
                    $query->prepare();
                }
            }

            Mage::helper('catalogsearch')->checkNotes();

            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');

            if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getParam('isAjax') == true) {
                $output['main'] = $this->getLayout()->getBlock('content')->toHtml();
                foreach($this->getLayout()->getAllBlocks() as $block){
                    if ($block->getType() == 'catalogsearch/layer'){
                        $output['layer'] = $block->toHtml();
                    }
                }
                $this->getResponse()->setHeader('Content-Type', 'application/json');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($output));
            } else $this->renderLayout();

            if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        } else {
            $this->_redirectReferer();
        }
    }
}
