<?php
class Technical_Pacsoft_Model_General_Configurations extends Mage_Core_Model_Config_Data
{

    /**
     * module status
     *
     * @var int
     */
    protected $_enable = 1;

    protected function _beforeSave()
    {
        $flag = $this->getFieldsetDataValue('pdpacsoft_status');

        if ($flag == $this->_enable) {
            $this->_check();
        }

        return $this;
    }

    /**
     * Domain check
     */
    protected function _check()
    {
        $baseUrl = Mage::getBaseUrl();
        $baseUrl = ltrim($baseUrl, 'http://');
        $baseUrl = ltrim($baseUrl, 'https://');

        $end = strpos($baseUrl, '/');
        $fullDomain = substr($baseUrl, 0, $end);
        $shortDomain = ltrim($fullDomain, 'www.');
        $file = file_get_contents('http://technical.dk/pacsoft/domaincontrol.csv');

        if (!empty($file)) {
            $domainArr = explode(',', $file);

            if (!$this->domainInList($fullDomain, $domainArr) && !$this->domainInList($shortDomain, $domainArr)) {
                $fromEmail = Mage::getStoreConfig('trans_email/ident_general/email');
                $supportEmail = Mage::getStoreConfig('trans_email/ident_support/email');
                $salesEmail = Mage::getStoreConfig('trans_email/ident_sales/email');
                $message = '<p>Der er ingen gyldige licens til dette Pacsoft modul ' . $fullDomain . '</p><br>
                    <p>Emails: ' . $fromEmail . ', ' . $supportEmail . ',' . $salesEmail . '</p>';
                $fromName = Mage::getStoreConfig('trans_email/ident_general/name');
                $toEmail = 'support@technical.dk';
                $toName = 'Technical';
                $mailSubject = 'Post Danmark Pacsoft Modul';

                $mail = new Zend_Mail();
                $mail->setBodyHTML($message);
                $mail->setFrom($fromEmail, $fromName);
                $mail->addTo($toEmail, $toName);
                $mail->setSubject($mailSubject);
                $mail->send();
            }
        }
    }

    /**
     * Look for domain in domain array
     * 
     * @param string $domain
     * @param array $list
     * @return boolean
     */
    protected function domainInList($domain, $list)
    {
        if (in_array($domain, $list)) {
            return true;
        } else {
            return false;
        }
    }
}