<?php
class Technical_Pacsoft_Model_Xml extends Mage_Core_Model_Abstract
{
    /**
     * Service types
     */
    protected $_serviceTypes = array(
        'pprivat'   => 'P19DK',
        'oprivat'   => 'P19DK',
        'pretur'    => 'P24DK',
        'eerhverv'  => 'PDKEP',
        'eretur'    => 'PDKEPR',
        'pprivatv'  => 'P19DK'
    );

    /**
     * Additional
     */
    protected $_additional = array(
        'pprivat' => 'DLV',
        'pprivatv' => 'PUPOPT'
    );

    /**
     * Generate xml file content
     *
     * @param string $type
     * @param array $ids
     * @return object|boolean
     */
    public function getPdpFile($type, $ids)
    {
        $customerId = Mage::getStoreConfig('pdpacsoft_options/settings/pdpacsoft_id');
        $quickId = Mage::getStoreConfig('pdpacsoft_options/settings/pdpacsoft_quickid');
        if ($quickId) {
            $counter = 0;
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?><data></data>");

            // adding meta information
            $meta = $xml->addChild('meta');
            $meta->addChild('val', $customerId)->addAttribute('n', 'origin');
            $meta->addChild('val', date('Y-m-d H:i'))->addAttribute('n', 'created');

            foreach($ids as $orderId) {
                $counter++;
                $receiverId = 'rec_' . $counter;
                $agentId = 'id_' . $counter;
                $order = Mage::getModel('sales/order');
                $order->load($orderId);
                $addresses = $order->getShippingAddress()->getStreet();

                $url = 'http://api.postnord.com/wsp/rest/BusinessLocationLocator/Logistics/ServicePointService_1.0/findNearestByAddress.xml?consumerId=' . $customerId . '&countryCode=DK&city=' . $order->getShippingAddress()->getCity() . '&postalCode=' . $order->getShippingAddress()->getPostcode() . '&streetName=' . $order->getShippingAddress()->getStreet();
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);

                $xmlResponse = new SimpleXMLElement($response);

                foreach($xmlResponse->servicePoints->servicePoint as $row) {
                    if($row->name == $order->getShippingAddress()->getCompany() && ($row->deliveryAddress->streetName . " " . $row->deliveryAddress->streetNumber) == array_shift($order->getShippingAddress()->getStreet()) ){
                        $agentId = $row->servicePointId;
                    }
                }

                // adding receiver information
                // if type is pprivatv then adding 2 receivers
                if ($type == 'pprivatv') {
                    // customer
                    $addresses2 = $order->getBillingAddress()->getStreet();
                    $receiver = $xml->addChild('receiver');
                    $receiver->addAttribute('rcvid', $receiverId);
                    $receiver->addChild('val', $order->getBillingAddress()->getFirstname() . ' ' . $order->getBillingAddress()->getLastname())->addAttribute('n','name');
                    $receiver->addChild('val', $addresses2[0])->addAttribute('n','address1');
                    $receiver->addChild('val', $addresses2[1])->addAttribute('n','address2');
                    $receiver->addChild('val', $order->getBillingAddress()->getPostcode())->addAttribute('n','zipcode');
                    $receiver->addChild('val', $order->getBillingAddress()->getCity())->addAttribute('n','city');
                    $receiver->addChild('val', 'DK')->addAttribute('n','country');
                    $receiver->addChild('val', $order->getBillingAddress()->getTelephone())->addAttribute('n','phone');
                    $receiver->addChild('val', $order->getCustomerEmail())->addAttribute('n','email');
                    $receiver->addChild('val', $order->getBillingAddress()->getTelephone())->addAttribute('n','sms');

                    // shop point
                    $rcPoint = $xml->addChild('receiver');
                    $rcPoint->addAttribute('rcvid', $agentId);
                    $rcPoint->addChild('val', $order->getShippingAddress()->getCompany())->addAttribute('n','name');
                    $rcPoint->addChild('val', $addresses[0])->addAttribute('n','address1');
                    $rcPoint->addChild('val', $addresses[1])->addAttribute('n','address2');
                    $rcPoint->addChild('val', $order->getShippingAddress()->getPostcode())->addAttribute('n','zipcode');
                    $rcPoint->addChild('val', $order->getShippingAddress()->getCity())->addAttribute('n','city');
                    $rcPoint->addChild('val', 'DK')->addAttribute('n','country');

                    // adding a partner
                    $partner = $rcPoint->addChild('partner');
                    $partner->addAttribute('parid', $this->_serviceTypes[$type]);
                    $partner->addChild('val', $agentId)->addAttribute('n','agentno');
                } else {
                    $receiver = $xml->addChild('receiver');
                    $receiver->addAttribute('rcvid', $receiverId);
                    if (($type == 'eerhverv' || $type == 'eretur') && $order->getShippingAddress()->getCompany()) {
                        $name = $order->getShippingAddress()->getCompany() . ' (' . $order->getShippingAddress()->getFirstname() . ' ' . $order->getShippingAddress()->getLastname() .')';
                        $receiver->addChild('val', $name)->addAttribute('n','name');
                    } else {
                        $receiver->addChild('val', $order->getShippingAddress()->getFirstname() . ' ' . $order->getShippingAddress()->getLastname())->addAttribute('n','name');
                    }
                    $receiver->addChild('val', $addresses[0])->addAttribute('n','address1');
                    $receiver->addChild('val', $addresses[1])->addAttribute('n','address2');
                    $receiver->addChild('val', $order->getShippingAddress()->getPostcode())->addAttribute('n','zipcode');
                    $receiver->addChild('val', $order->getShippingAddress()->getCity())->addAttribute('n','city');
                    $receiver->addChild('val', 'DK')->addAttribute('n','country');
                    if ($order->getShippingAddress()->getTelephone()) {
                        $receiver->addChild('val', $order->getShippingAddress()->getTelephone())->addAttribute('n','phone');
                        $receiver->addChild('val', $order->getShippingAddress()->getTelephone())->addAttribute('n','sms');
                    } else {
                        $receiver->addChild('val', $order->getBillingAddress()->getTelephone())->addAttribute('n','phone');
                        $receiver->addChild('val', $order->getBillingAddress()->getTelephone())->addAttribute('n','sms');
                    }
                    $receiver->addChild('val', $order->getCustomerEmail())->addAttribute('n','email');
                }

                // adding shipment information
                $shipment = $xml->addChild('shipment');
                $shipment->addAttribute('orderno', $order->getIncrementId());
                if ($type == 'pprivatv') {
                     $shipment->addChild('val', $agentId)->addAttribute('n','agentto');
                }
                $shipment->addChild('val', $quickId)->addAttribute('n','from');
                $shipment->addChild('val', $receiverId)->addAttribute('n','to');
                $service = $shipment->addChild('service');
                $service->addAttribute('srvid', $this->_serviceTypes[$type]);

                // adding addons
                $emailAddon = $service->addChild('addon');
                $emailAddon->addAttribute('adnid', 'notemail');
                $emailAddon->addChild('val', $order->getCustomerEmail())->addAttribute('n', 'email');

                $smsAddon = $service->addChild('addon');
                $smsAddon->addAttribute('adnid', 'notsms');
                $smsAddon->addChild('val', $order->getShippingAddress()->getTelephone())->addAttribute('n','phone');

                if ($this->_additional[$type]) {
                    $service->addChild('addon')->addAttribute('adnid', $this->_additional[$type]);
                }

                // adding container
                $container = $shipment->addChild('container');
                $container->addAttribute('type', 'parcel');
                $container->addChild('val', 1)->addAttribute('n', 'copies');

                // adding Pre-notification by e-mail
                $ufonline = $shipment->addChild('ufonline');
                $option = $ufonline->addChild('option');
                $option->addAttribute('optid', 'enot');
                $option->addChild('val', Mage::getStoreConfig('trans_email/ident_sales/email'))->addAttribute('n', 'from');
                $option->addChild('val', $order->getCustomerEmail())->addAttribute('n', 'to');
            }
            return $xml;
        } else {
            return false;
        }
    }
}