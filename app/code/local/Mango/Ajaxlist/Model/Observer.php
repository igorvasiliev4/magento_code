<?php
class Mango_Ajaxlist_Model_Observer {
    /* remove ajax parameter from resulting html */

    public function http_response_send_before($observer) {
        $response = $observer->getResponse();
        $html = $response->getBody();
        $_ajaxparameter = Mage::helper("ajaxlist")->getAjaxParameter();
        if (Mage::app()->getRequest()->getParam($_ajaxparameter) && !Mage::app()->getStore()->isAdmin()) {
            $_url_helper = Mage::helper('core/url');
            $_uec = $_url_helper->getEncodedUrl();
            $_ajaxparameter = Mage::helper("ajaxlist")->getAjaxParameter();
            $_new_uenc = $_url_helper->urlEncode($_url_helper->removeRequestParam($_url_helper->getCurrentUrl(), $_ajaxparameter));
            $html = str_replace($_uec, $_new_uenc, $html);
        }
        $response->setBody($html);
    }

    /* add layout handlers */

    public function addLayoutHandler($observer) {
        $_ajaxparameter = Mage::helper("ajaxlist")->getAjaxParameter();
        $_is_ajax_page = Mage::app()->getRequest()->getParam($_ajaxparameter);
        //if ($_is_ajax_page) {
        $action = $observer->getEvent()->getAction();
        //print_r($action->getRequest());
        if (($action->getRequest()->getModuleName() == 'catalog' && $action->getRequest()->getControllerName() == 'category') || $action->getRequest()->getModuleName() == 'catalogsearch') {
            $layout = $observer->getEvent()->getLayout();
            if ($_is_ajax_page)
                $layout->getUpdate()->addHandle("AJAXLIST_PAGE_HANDLER");
            $_uses_layered_navigation = false;
            if (Mage::registry('current_category') && Mage::registry('current_category')->getIsAnchor()) {
                if ($_is_ajax_page)
                    $layout->getUpdate()->addHandle("AJAXLIST_CATEGORY_LAYERED_HANDLER");
                $_uses_layered_navigation = true;
            }elseif ($action->getRequest()->getModuleName() == 'catalogsearch' && $action->getRequest()->getControllerName() == 'result' && $action->getRequest()->getActionName() == 'index') {
                if ($_is_ajax_page)
                    $layout->getUpdate()->addHandle("AJAXLIST_SEARCH_RESULT_HANDLER");
                $_uses_layered_navigation = true;
            }
            if (Mage::getStoreConfig("ajaxlist/frontend/horizontal_only") && $_uses_layered_navigation) {
                Mage::app()->getLayout()->getUpdate()->addUpdate('<remove name="ajax_layered_navigation_left"/><remove name="catalog.leftnav"/><remove name="catalogsearch.leftnav"/>');
                Mage::register("use_horizontal_layered_navigation_only", true);
            }
        }
    }

    /* use this function to add the div#ajaxlist-reload-product_list container to the products list
     * and to inset the top layered navigation on top of the products list before displaying.
     *  */

    public function core_block_abstract_to_html_after(Varien_Event_Observer $observer) {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Catalog_Block_Product_List) { /* will insert layered navigation block on top of the products list.. */
            $_block_code = $block->getNameInLayout();
            if (!in_array($_block_code, array("product_list", "search_result_list")))
                return;
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            $html = '<div id="ajaxlist-reload-product_list">' . Mage::registry("top_layered_navigation_html") . $html . "</div>";
            $transport->setHtml($html);
        }elseif ($block instanceof Mage_Catalog_Block_Product_Compare_Abstract) {
            $_block_code = $block->getNameInLayout();
            if (!Mage::registry("ajaxcompare_check_code")) {
                $_check_code = explode(",", Mage::getStoreConfig("ajaxlist/ajaxcompare/reload_block"));
                Mage::register("ajaxcompare_check_code", $_check_code);
            }
            $_check_code = Mage::registry("ajaxcompare_check_code");
            if (count($_check_code) && in_array($_block_code, $_check_code)) {
                $transport = $observer->getTransport();
                $html = $transport->getHtml();
                $_code = str_replace(".", '-', $_block_code);
                $transport->setHtml('<div id="ajaxcompare-reload-block-' . $_code . '">' . $html . "</div>");
            }
        }
        return;
    }
    /* SET TEMPLATES FOR EACH FILTER */

    public function core_block_abstract_prepare_layout_after(Varien_Event_Observer $observer) {
        $front = Mage::app()->getRequest()->getRouteName();
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();
        // Perform this operation if we're on a category view page or search results page
        if (($front == 'catalog' && $controller == 'category' && $action == 'view') || ($front == 'catalogsearch' && $controller == 'result' && $action == 'index')) {
            $block = $observer->getBlock();
            if ($block instanceof Mage_Catalog_Block_Layer_View) {
                $attributes = $block->getLayer()->getFilterableAttributes();
                foreach ($attributes as $_attribute) {
                    $_code = $_attribute->getAttributeCode();
                    if ($_code == 'price') {
                        if (Mage::getStoreConfig("ajaxlist/ajaxlist/use_priceslider")) {
                            $block->getChild($_attribute->getAttributeCode() . '_filter')->setTemplate("ajaxlayerednavigation/catalog/layer/price.phtml");
                        } else {
                            $block->getChild($_attribute->getAttributeCode() . '_filter')->setTemplate("ajaxlayerednavigation/catalog/layer/attribute.phtml");
                        }
                    } else {
                        if (Mage::helper('core')->isModuleEnabled('Mage_ConfigurableSwatches') && Mage::helper('configurableswatches')->attrIsSwatchType($_attribute)) {
                            $block->getChild($_attribute->getAttributeCode() . '_filter')->setTemplate('ajaxlayerednavigation/catalog/layer/attribute_configurableswatches.phtml');
                        } else {
                            $block->getChild($_attribute->getAttributeCode() . '_filter')->setTemplate("ajaxlayerednavigation/catalog/layer/attribute.phtml");
                        }
                    }
                }
            } elseif ($block instanceof Mage_Catalog_Block_Layer_Filter_Category) {
                if (Mage::app()->getRequest()->getModuleName() == "catalog") {
                    $block->setTemplate('ajaxlayerednavigation/catalog/layer/category.phtml');
                } else {
                    $block->setFilterId('cat')->setTemplate('ajaxlayerednavigation/catalog/layer/attribute.phtml');
                }
            } elseif ($block instanceof Mage_Catalog_Block_Layer_State) {
                $block->setTemplate("ajaxlayerednavigation/catalog/layer/state.phtml");
            } elseif ($block instanceof Mage_Catalog_Block_Product_List) { /* insert top layered navigation block before products list */
                if (Mage::registry("use_horizontal_layered_navigation_only") && !Mage::registry("top_layered_navigation_html") ) {
                    // Perform this operation if we're on a category view page or search results page
                    $_block_class = "";
                    if ($front == 'catalog' && $controller == 'category' && $action == 'view')/* set the layered navigation block */
                        $_block_class = "Mage_Catalog_Block_Layer_View";
                    elseif ($front == 'catalogsearch' && $controller == 'result' && $action == 'index')
                        $_block_class = "Mage_CatalogSearch_Block_Layer";
                    $_top_layered_navigation_html = ( $_block_class != "" ) ? Mage::app()->getLayout()->createBlock($_block_class, 'horizontal_layered', array('template' => 'ajaxlayerednavigation/catalog/layer/view-horizontal.phtml')
                            )->toHtml() : "";
                    Mage::register("top_layered_navigation_html", $_top_layered_navigation_html);
                }
            }
        }
        return;
    }

    public function controller_response_redirect(Varien_Event_Observer $observer) {
        /* we know all the add compare operations were finished, 
         * so we stop the redirection and only show
         * the ajax json response */
        $_r = Mage::app()->getRequest();
        if (!Mage::app()->getStore()->isAdmin() && $_r->getRouteName() == "catalog" &&
                $_r->getControllerName() == "product_compare" && in_array($_r->getActionName(), array("add", "remove", "clear")) && $_r->getParam("ajaxlist")) {
            Mage::helper("ajaxlist/compare")->returnResultJsonResponse($observer->getResponse());
        }
    }

}
