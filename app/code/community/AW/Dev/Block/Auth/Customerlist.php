<?php

class AW_Dev_Block_Auth_Customerlist extends Mage_Core_Block_Template
{
    protected $_customerGroups = array();

    protected function _beforeToHtml()
    {
        if (!$this->getTemplate()) {
            $this->setTemplate('aw_dev/auth/customerlist.phtml');
        }
        return parent::_beforeToHtml();
    }

    public function getCustomers()
    {
        $customers = Mage::getModel('customer/customer')->getCollection();
        $customers->addNameToSelect();
        $customers->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());
        return $customers;
    }

    public function getLoginUrl(Mage_Customer_Model_Customer $customer)
    {
        return $this->getUrl('awdev/customer/login', array('customer_email' => $customer->getData('email')));
    }

    public function getCustomerGroupCode($customer)
    {
        $groupId = $customer->getGroupId();
        if(!isset($this->_customerGroups[$groupId])) {
            $this->_customerGroups[$groupId] = $group = Mage::getModel('customer/group')->load($groupId);
        }
        return $this->_customerGroups[$groupId]->getCode();
    }
}
