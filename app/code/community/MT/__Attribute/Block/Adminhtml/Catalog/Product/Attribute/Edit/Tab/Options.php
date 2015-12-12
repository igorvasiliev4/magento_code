<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Attribute_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Options extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract{
    public function __construct(){
        parent::__construct();
        $this->setTemplate('mt/attribute/catalog/product/attribute/options.phtml');
    }

    protected function _prepareLayout(){
        $this->setChild('quickAddBtn', $this->getLayout()->createBlock('adminhtml/widget_button', null, array(
            'label'     => Mage::helper('mtattribute')->__('Quick Add'),
            'onclick'   => 'attributeOption.quickAdd()',
            'class'     => 'add',
            'type'      => 'button'
        )));

        parent::_prepareLayout();
    }

    /**
     * Retrieve attribute option values if attribute input type select or multiselect
     * Override to add image into attribute option object
     *
     * @return array
     * @version 1.7.0.2
     */
    public function getOptionValues()
    {
        $attributeType = $this->getAttributeObject()->getFrontendInput();
        $defaultValues = $this->getAttributeObject()->getDefaultValue();
        if ($attributeType == 'select' || $attributeType == 'multiselect') {
            $defaultValues = explode(',', $defaultValues);
        } else {
            $defaultValues = array();
        }

        switch ($attributeType) {
            case 'select':
                $inputType = 'radio';
                break;
            case 'multiselect':
                $inputType = 'checkbox';
                break;
            default:
                $inputType = '';
                break;
        }

        $values = $this->getData('option_values');
        if (is_null($values)) {
            $values = array();
            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
                ->setPositionOrder('desc', true)
                ->load();

            foreach ($optionCollection as $option) {
                $value = array();
                if (in_array($option->getId(), $defaultValues)) {
                    $value['checked'] = 'checked="checked"';
                } else {
                    $value['checked'] = '';
                }

                $value['intype'] = $inputType;
                $value['id'] = $option->getId();
                $value['sort_order'] = $option->getSortOrder();
                // PATCH
                $value['thumb'] = $option->getThumb();
                $value['image'] = $option->getImage();
                // END PATCH
                foreach ($this->getStores() as $store) {
                    $storeValues = $this->getStoreOptionValues($store->getId());
                    if (isset($storeValues[$option->getId()])) {
                        $value['store'.$store->getId()] = htmlspecialchars($storeValues[$option->getId()]);
                    }
                    else {
                        $value['store'.$store->getId()] = '';
                    }
                }
                $values[] = new Varien_Object($value);
            }
            $this->setData('option_values', $values);
        }

        return $values;
    }
}
