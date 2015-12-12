<?php

class AW_Dev_CustomerController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    public function loginAction()
    {
        $customerEmail = $this->getRequest()->getParam('customer_email');
        if($customerEmail) {
            $customer = Mage::getModel('customer/customer');
            $customer
                ->setData('website_id', Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($customerEmail);
            if($customer->getData()) {
                $customerSession = Mage::getSingleton('customer/session');
                $customerSession->setCustomerAsLoggedIn($customer);
                if(method_exists($customerSession, 'renewSession')) {
                    $customerSession->renewSession();
                }
            } else {
                $this->_getSession()->addError($this->__('Couldn\'t load customer by given email'));
            }
        } else {
            $this->_getSession()->addError($this->__('Customer email isn\'t specified'));
        }
        return $this->_redirectReferer();
    }
}
