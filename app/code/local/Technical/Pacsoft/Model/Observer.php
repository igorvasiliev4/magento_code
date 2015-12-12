<?php
class Technical_Pacsoft_Model_Observer
{
    /**
     * Adds options to order grid mass actions 
     *
     * @param object $observer
     */
    public function addPDPMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction' && $block->getRequest()->getControllerName() == 'sales_order') {
            $block->addItem('POSTDK PRIVAT', array(
                'label' => 'POSTDK PRIVAT',
                'url' => Mage::app()->getStore()->getUrl('pdpacsoft/adminhtml_xml/pprivat'),
            ));
            $block->addItem('POST DK PRIVAT UDEN OMDELING', array(
                'label' => 'POST DK PRIVAT UDEN OMDELING',
                'url' => Mage::app()->getStore()->getUrl('pdpacsoft/adminhtml_xml/oprivat'),
            ));
            $block->addItem('POSTDK RETUR', array(
                'label' => 'POSTDK RETUR',
                'url' => Mage::app()->getStore()->getUrl('pdpacsoft/adminhtml_xml/pretur'),
            ));
            $block->addItem('POST DK ERHVERV', array(
                'label' => 'POST DK ERHVERV',
                'url' => Mage::app()->getStore()->getUrl('pdpacsoft/adminhtml_xml/eerhverv'),
            ));
            $block->addItem('POST DK ERHVERV RETUR', array(
                'label' => 'POST DK ERHVERV RETUR',
                'url' => Mage::app()->getStore()->getUrl('pdpacsoft/adminhtml_xml/eretur'),
            ));
        }
    }

    /**
     * Cut the shipping title from shipping description
     *
     * @param object $event
     */
    public function changeShippingDescription($event)
    {
        $order = $event->getEvent()->getOrder();
        $fullDescr = $order->getShippingDescription();
        $delimiter = strpos($fullDescr, '-');
        if ($delimiter) {
            $shortDescr = substr($fullDescr, $delimiter+2);
            $order->setShippingDescription($shortDescr);
        }
    }

    /**
     * Clear pacsoft pakkeshop address status from session
     */
    public function clearPacsoftPakkeshop()
    {
        $session = Mage::getSingleton('checkout/session');
        if ($session->getPdpAddr()) {
            $session->unsPdpAddr();
        }
    }
}
