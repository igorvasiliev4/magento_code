<?php
class Technical_Pacsoft_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Pacsoft response statuses
     */
    protected $_statuses = array(
        '201' => true,
        '403' => 'Error: Please check user ID, password and that both the Pacsoft Online account and the Unifaun ERPConnect functionality are active.',
        '500' => 'Error: Please check the order file for syntax errors.',
        'exception' => 'Error: Wrong answer from Pacsoft system.'
    );

    /**
     * Send order xml to Pacsoft
     *
     * @param object $orderXml
     * @return mixed
     */
    public function sendOrder($orderXml)
    {
        $user = Mage::getStoreConfig('pdpacsoft_options/settings/pdpacsoft_user');
        $password = Mage::getStoreConfig('pdpacsoft_options/settings/pdpacsoft_pass');
        $developerId = '000000000';

        $url = 'https://www.pacsoftonline.com/ufoweb/order?session=po_DK&user=' . $user . '&pin=' . $password . '&developerid='.$developerId.'&type=xml';
        $xml = $orderXml->asXML();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                               'Content-type: application/xml',
                                               'Content-length: ' . strlen($xml)
                                             ));
        $response = curl_exec($ch);
        curl_close($ch);

        $xmlResponse = new SimpleXMLElement($response);
        $status = (string)$xmlResponse->val[1];
        if ($status && isset($this->_statuses[$status])) {
            return $this->_statuses[$status];
        } else {
            return  $this->_statuses['exception'];
        }
    }

    /**
     * Gather link with autologin in pacsoft
     *
     * @return string
     */
    public function getLoginLink()
    {
        $company = Mage::getStoreConfig('pdpacsoft_options/settings/pdpacsoft_user');
        $pass = Mage::getStoreConfig('pdpacsoft_options/settings/pdpacsoft_pass');
        $link = 'https://www.pacsoftonline.com/webapp?Env=po_DK&Action=act_SystemActions_AutoLogin&Company=' . $company . '&Pass=' . $pass . '&Menu=Printing&Body=act_MenuActions_Item_ItemHandler_ShipmentJobSearchActions';
        return $link;
    }
}