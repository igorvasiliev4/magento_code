 <?php
/**
 * Apptha
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://www.apptha.com/LICENSE.txt
*
* ==============================================================
*                 MAGENTO EDITION USAGE NOTICE
* ==============================================================
* This package designed for Magento COMMUNITY edition
* Apptha does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* Apptha does not provide extension support in case of
* incorrect edition usage.
* ==============================================================
*
* @category    Apptha
* @package     Apptha_Marketplace
* @version     0.1.7
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
*
*/
?>
<?php
class  Apptha_Marketplace_Block_Order_History  extends Mage_Sales_Block_Order_History {
	
	public function __construct()
	{
	$data = $status = $selectFilter = $from = $to = '';
	$data = $this->getRequest()->getPost();
	if (isset($data['status'])) {
		$status = $data['status'];
	}
	if (isset($data['select_filter'])) {
		$selectFilter = $data['select_filter'];
	}
	if (!empty($selectFilter)) {
		switch ($selectFilter) {
			case "today":
				/**
				 * today interval
				 */
				$startDay = strtotime("-1 today midnight");
				$endDay = strtotime("-1 tomorrow midnight");
				$from = date("Y-m-d", $startDay);
				$to = date("Y-m-d", $endDay);
				$fromDisplay = date("Y-m-d", $startDay);
				$toDisplay = date("Y-m-d", $startDay);
				break;
			case "yesterday":
				/**
				 *  yesterday interval
				 */
				$startDay = strtotime("-1 yesterday midnight");
				$endDay = strtotime("-1 today midnight");
				$from = date("Y-m-d", $startDay);
				$to = date("Y-m-d", $endDay);
				$fromDisplay = date("Y-m-d", $startDay);
				$toDisplay = date("Y-m-d", $startDay);
				break;
			case "lastweek":
				/**
				 *  last week interval
				 */
				$to = date('d-m-Y');
				$toDay = date('l', strtotime($to));
				/**
				 *  if today is monday, take last monday
				*/
				if ($toDay == 'Monday') {
					$startDay = strtotime("-1 monday midnight");
					$endDay = strtotime("yesterday");
				} else {
					$startDay = strtotime("-2 monday midnight");
					$endDay = strtotime("-1 sunday midnight");
				}
				$from = date("Y-m-d", $startDay);
				$to = date("Y-m-d", $endDay);
				$to = date('Y-m-d', strtotime($to . ' + 1 day'));
				$fromDisplay = $from;
				$toDisplay = date("Y-m-d", $endDay);
				break;
			case "lastmonth":
				/**
				 *  last month interval
				 */
				$from = date('Y-m-01', strtotime('last month'));
				$to = date('Y-m-t', strtotime('last month'));
				$to = date('Y-m-d', strtotime($to . ' + 1 day'));
				$fromDisplay = $from;
				$toDisplay = date('Y-m-t', strtotime('last month'));
				break;
			case "custom":
				/**
				 *  last custom interval
				 */
				$from = date('Y-m-d', strtotime($data['date_from']));
				$to = date('Y-m-d', strtotime($data['date_to'] . ' + 1 day'));
				$fromDisplay = $from;
				$toDisplay = date('Y-m-d', strtotime($data['date_to']));
				break;
		}
	}
	
	$dbFrom = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
	$dbTo = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
	parent::__construct();
	$this->setTemplate('sales/order/history.phtml');

	$orders = Mage::getResourceModel('sales/order_collection')
	->addFieldToSelect('*')
	->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
	->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()));
	if ($status != '') {
		$orders->addFieldToFilter('status', array('in' => array($status)));
	}
	if ($selectFilter != '') {
		$orders->addFieldToFilter('created_at', array('from' => $dbFrom, 'to' => $dbTo));
	}
	$orders->setOrder('created_at', 'desc');

	$this->setOrders($orders);

	Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
}

protected function _prepareLayout()
{
	parent::_prepareLayout();

	$pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
	->setCollection($this->getOrders());
	$this->setChild('pager', $pager);
	$this->getOrders()->load();
	return $this;
}

public function getPagerHtml()
{
	return $this->getChildHtml('pager');
}

public function getViewUrl($order)
{
	return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
}

public function getTrackUrl($order)
{
	return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
}

public function getReorderUrl($order)
{
	return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
}

public function getBackUrl()
{
	return $this->getUrl('customer/account/');
}

}

?>