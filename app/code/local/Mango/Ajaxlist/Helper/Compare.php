<?php

class Mango_Ajaxlist_Helper_Compare extends Mage_Core_Helper_Abstract {

    public function returnResultJsonResponse($response) {
        $layout = Mage::app()->getLayout();
        $layout->getUpdate()->addHandle("default")->load();
        $layout->generateXml()->generateBlocks();
        $_json_response = array();
        $_reload_block = explode(",", trim(Mage::getStoreConfig("ajaxlist/ajaxcompare/reload_block")));
        foreach ($_reload_block as $_block_code) {
             $_block = $layout->getBlock($_block_code);
              if ($_block) {
                  $_code = str_replace(".", '-', $_block_code);
                  $_json_response[  'ajaxcompare_reload_block'][$_code] = $_block->toHtml();
              }
        }     
        
        
        $_json_response["message"] = $layout->createBlock("core/messages")->setMessages(Mage::getSingleton('catalog/session')->getMessages(true))->toHtml();     //->getBlock('global_messages')->toHtml();
        $this->_sendJsonResponse($response, $_json_response);
    }

    private function _sendJsonResponse($response, $_json_response) {
        $response->clearHeaders()
                ->setHeader('Content-Type', 'application/json')
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setBody(json_encode($_json_response))
                ->sendResponse();
        exit;
    }

}
