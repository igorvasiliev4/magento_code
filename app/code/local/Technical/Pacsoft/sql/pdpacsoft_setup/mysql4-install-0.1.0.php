<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$this->startSetup();

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'shipping_description',
    "varchar(255) not null default ''"
);

$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'shipping_description',
    'shipping_description'
);

$select = $this->getConnection()->select();
$select->join(
    array('order_shipping'=>$this->getTable('sales/order')),
    $this->getConnection()->quoteInto(
        'order_shipping.entity_id = order_grid.entity_id',
        Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
    ),
    array('shipping_description' => 'shipping_description')
);

$this->getConnection()->query(
    $select->crossUpdateFromSelect(
        array('order_grid' => $this->getTable('sales/order_grid'))
    )
);

$this->endSetup();