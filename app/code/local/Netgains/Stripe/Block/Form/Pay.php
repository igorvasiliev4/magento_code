<?php
class Netgains_Stripe_Block_Form_Pay extends Mage_Payment_Block_Form_Ccsave
{
	 protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('stripe/form/stripe.phtml');
    }
}
