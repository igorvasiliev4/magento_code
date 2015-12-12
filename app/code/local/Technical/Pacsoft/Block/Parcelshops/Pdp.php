<?php

class Technical_Pacsoft_Block_Parcelshops_Pdp extends Mage_Core_Block_Abstract
{
    /**
     * Create pacsoft point pop-up
     *
     * @param string $requestedZip
     * @return string
     */
	public function getNearestShops($requestedZip)
	{
        $customerId = Mage::getStoreConfig('pdpacsoft_options/settings/pdpacsoft_id');
        $xmlResponse = file_get_contents('http://api.postnord.com/wsp/rest/BusinessLocationLocator/Logistics/ServicePointService_1.0/findNearestByAddress.xml?consumerId=' . $customerId . '&countryCode=DK&postalCode='. $requestedZip . '&numberOfServicePoints=10');
        $points = array();
        $helper = Mage::helper('pdpacsoft');

        $html = '<div id="pdp-overlay" class="pdp-overlay"></div>
            <div id="pdp-addr" class="pdp-address">
                <div class="close-link">
                    <a href="javascript:void(0);" onclick="closePdp()">' . $this->__('Close') . '</a>
                </div>';

        if (!$xmlResponse) {
            $html .= '<div>' . $helper->__('Please type correct Postcode') . '</div></div>';
            return $html;
        }

        $xmlShops = new SimpleXMLElement($xmlResponse);

        $html .= '<table id="pdp-shops" class="pdp-table" border="0" align="center"><tr>
            <th>&nbsp</th>
            <th><strong>' . $helper->__('Company') . '</strong></th>
            <th><strong>' . $helper->__('Street') . '</strong></th>
            <th><strong>' . $helper->__('ZipCode') . '</strong></th>
            <th class="pdp-time"><strong>' . $helper->__('Monday') . '</strong></th>
            <th class="pdp-time"><strong>' . $helper->__('Tuesday') . '</strong></th>
            <th class="pdp-time"><strong>' . $helper->__('Wednesday') . '</strong></th>
            <th class="pdp-time"><strong>' . $helper->__('Thursday') . '</strong></th>
            <th class="pdp-time"><strong>' . $helper->__('Friday') . '</strong></th>
            <th class="pdp-time"><strong>' . $helper->__('Saturday') . '</strong></th>
            <th class="pdp-time"><strong>' . $helper->__('Sunday') . '</strong></th></tr>';

        $counter = 0;
        foreach($xmlShops->servicePoints->servicePoint as $shop) {
            $counter++;
            $pointKey = 'point-'. $counter;
            $points[$pointKey] = array(
                'company' => trim($shop->name),
                'street' => trim($shop->deliveryAddress->streetName) . ' ' . trim($shop->deliveryAddress->streetNumber),
                'zipcode' => trim($shop->deliveryAddress->postalCode),
                'city' => trim($shop->deliveryAddress->city)
            );

            $html .= '<tr>
                <td><input type="radio" value="'. $pointKey .'" name="shop"/></td>
                <td>' . trim($shop->name) . '</td>
                <td>' . trim($shop->deliveryAddress->streetName) . ' ' . trim($shop->deliveryAddress->streetNumber) . '</td>
                <td>' . trim($shop->deliveryAddress->postalCode) . '</td>';
            $temp = 0;
            if (!empty($shop->openingHours)) {
                foreach($shop->openingHours->openingHour as $weekday) {
                    $temp++;
                    $html .= '<td class="pdp-time">' . $this->pdpTime($weekday->from1) . ' - ' . $this->pdpTime($weekday->to1) . '</td>';
                }
            }
            if ($temp < 7) {
                while ($temp < 7) {
                    $temp++;
                    $html .= '<td class="pdp-time">&nbsp</td>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</table>
            <div class="pdp-ok" onclick="pdpChoose(\''. Mage::getUrl('pdpacsoft/pdp/shipping') .'\')">'. $helper->__('OK') .'</div>
            </div>';

        $session = Mage::getSingleton('core/session');
        if (!$session->getPdppoints()) {
            $session->setData('pdppoints', $points);
        } else {
            $session->unsetData('pdppoints');
            $session->setData('pdppoints', $points);
        }

        return $html;
    }

    /**
     * Format time
     *
     * @param string $time
     * @return string
     */
    public function pdpTime($time)
    {
        $hours = substr($time, 0, 2);
        $minutes = substr($time, 2, 2);
        $pdpTime = $hours . ':' . $minutes;
        return $pdpTime;
    }
}