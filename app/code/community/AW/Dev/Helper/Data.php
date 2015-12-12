<?php

class AW_Dev_Helper_Data extends Mage_Core_Helper_Data
{
    const NOTIFICATIONS_DISABLED = 'aw_dev_notifications_disabled';

    public function getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    public function disableNotifications()
    {
        if (!$this->getAdminSession()->getData(self::NOTIFICATIONS_DISABLED)) {
            $collection = Mage::getModel('core/config_data')->getCollection();
            $collection->getSelect()->where('path LIKE ?', 'advanced/modules_disable_output/Mage_AdminNotification');
            if ($collection->getSize()) {
                foreach ($collection as $configItem) {
                    $configItem->setValue(1)->save();
                }
            } else {
                $disableEntry = Mage::getModel('core/config_data');
                $disableEntry->setData(array(
                    'path' => 'advanced/modules_disable_output/Mage_AdminNotification',
                    'value' => 1
                ));
                $disableEntry->save();
            }
            $this->getAdminSession()->setData(self::NOTIFICATIONS_DISABLED, true);
        }
    }
}
