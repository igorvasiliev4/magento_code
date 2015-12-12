<?php

class Technical_Pacsoft_PdpController extends Mage_Core_Controller_Front_Action
{
    public function zipAction()
    {
        if ($this->getRequest()->isPost()) {
            $zipCode = $this->getRequest()->getPost('zip_code');
            $html = Mage::getBlockSingleton('pdpacsoft/parcelshops_pdp')->getNearestShops($zipCode);
            echo $html;
            die();
        }
    }

    public function shippingAction()
    {
        if ($this->getRequest()->isPost()) {
            $shopId = $this->getRequest()->getPost('shop_id');
            Mage::getModel('pdpacsoft/pdp')->fillShippingAddress($shopId);
            die();
        }
    }

    public function defShippingAction()
    {
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getPost('reset')) {
                Mage::getModel('pdpacsoft/pdp')->resetShippingAddress();
                die();
            } else {
                $response = Mage::getSingleton('checkout/session')->getPdpAddr() ? true : false;
                $this->getResponse()
                    ->clearHeaders()
                    ->setHeader('Content-Type', 'application/json')
                    ->setBody(json_encode($response));
            }
        }
    }
}