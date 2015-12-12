<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */

include_once 'Mage/Catalog/controllers/CategoryController.php';
class MT_Filter_Catalog_CategoryController extends Mage_Catalog_CategoryController{
    /**
     * Category view action
     * @version 1.6.2.0, 1.7.0.2
     */
    public function viewAction(){
        if ($category = $this->_initCatagory()) {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            if (!$category->hasChildren()) {
                $update->addHandle('catalog_category_layered_nochildren');
            }

            $this->addActionLayoutHandles();
            $update->addHandle($category->getLayoutUpdateHandle());
            $update->addHandle('CATEGORY_' . $category->getId());
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');

            if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getParam('isAjax') == true){
                $output['main'] = $this->getLayout()->getBlock('content')->toHtml();
                foreach($this->getLayout()->getAllBlocks() as $block){
                    if ($block->getType() == 'catalog/layer_view'){
                        $output['layer'] = $block->toHtml();
                    }
                }
                $this->getResponse()->setHeader('Content-Type', 'application/json');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($output));
            }else $this->renderLayout();
        }elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
