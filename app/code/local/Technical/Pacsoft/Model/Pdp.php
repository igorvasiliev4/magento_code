<?php
class Technical_Pacsoft_Model_Pdp extends Mage_Core_Model_Abstract
{
    /**
     * Sets shipping address
     *
     * @param int $id
     * @return boolean
     */
    public function fillShippingAddress($id)
    {
        $session = Mage::getSingleton('core/session');
        $shops = $session->getPdppoints();

        if ($shops && $id) {
            $address = $shops[$id];
            $cart = Mage::getSingleton('checkout/cart');
            $name = $cart->getQuote()->getBillingAddress()->getFirstname();
            $surname = $cart->getQuote()->getBillingAddress()->getLastname();

            $cart->getQuote()->getShippingAddress()->setFirstname($name)
                ->setLastname($surname)
                ->setCountryId('DK')
                ->setPostcode($address['zipcode'])
                ->setCompany($address['company'])
                ->setCity($address['city'])
                ->setTelephone(' - ')
                ->setSaveInAddressBook(0)
                ->setSameAsBilling(0)
                ->setStreet(array($address['street'], ''));
            $cart->save();

            Mage::getSingleton('checkout/session')->setPdpAddr(true);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Reset shipping address
     *
     * @return boolean
     */
    public function resetShippingAddress()
    {
        $cart = Mage::getSingleton('checkout/cart');

        $cart->getQuote()->getShippingAddress()->setFirstname('')
            ->setLastname('')
            ->setCountryId('DK')
            ->setPostcode('')
            ->setCompany('')
            ->setCity('')
            ->setTelephone('')
            ->setSaveInAddressBook(0)
            ->setSameAsBilling(0)
            ->setStreet(array('', ''));
          $cart->save();

          Mage::getSingleton('checkout/session')->setPdpAddr(false);

          return true;
    }
}