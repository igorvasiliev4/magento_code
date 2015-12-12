<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Attribute_Block_Catalog_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable{
    /**
     * Get allowed attributes
     * Override to add image to attribute option
     *
     * @return array
     * @version 1.7.0.2
     */
    public function getAllowAttributes(){
        $attributes = $this->getProduct()->getTypeInstance(true)->getConfigurableAttributes($this->getProduct());
        foreach ($attributes as $attribute){
            if (!$attribute->getOption()){
                $productAttribute = $attribute->getProductAttribute();
                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($productAttribute->getAttributeId())
                    ->load();
                $options = array();
                foreach ($optionCollection as $option){
                    if ($option->getThumb()){
                        $options[] = array(
                            'option_id' => $option->getOptionId(),
                            'thumb' => $option->getThumb()
                        );
                    }
                }
                if (count($options)) $attribute->setOption($options);
            }
        }
        return $attributes;
    }

    /**
     * Composes configuration for js
     * Override to add image to attribute option
     *
     * @return string
     * @version 1.7.0.2
     */
    public function getJsonConfig(){
        $attributes = array();
        $options    = array();
        $origins    = array();
        $images     = array();
        $thumbs     = array();
        $store      = $this->getCurrentStore();
        $taxHelper  = Mage::helper('tax');
        $currentProduct = $this->getProduct();

        $preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
        if ($preconfiguredFlag) {
            $preconfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }

        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();
            $product->load('image');

            foreach($product->getMediaGalleryImages() as $item){
                $origins[$product->getId()][$item->getId()] = $item->getUrl();
                $images[$product->getId()][$item->getId()]  = (string)Mage::helper('catalog/image')->init($product, 'image', $item->getFile())->resize(265);
                $thumbs[$product->getId()][$item->getId()]  = (string)Mage::helper('catalog/image')->init($product, 'thumbnail', $item->getFile())->resize(56);
            }

            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }
                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($currentProduct->getFinalPrice())
        );

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            // PATCH
            $optionCollection = $attribute->getOption();
            $optionImages = array();
            if (is_array($optionCollection)){
                foreach ($optionCollection as $option){
                    $optionImages[$option['option_id']] = $option['thumb'];
                }
            }
            // END PATCH
            $attributeId = $productAttribute->getId();
            $info = array(
                'id'        => $productAttribute->getId(),
                'code'      => $productAttribute->getAttributeCode(),
                'label'     => $attribute->getLabel(),
                'options'   => array()
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
                    $currentProduct->setConfigurablePrice(
                        $this->_preparePrice($value['pricing_value'], $value['is_percent'])
                    );
                    $currentProduct->setParentId(true);
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        array('product' => $currentProduct)
                    );
                    $configurablePrice = $currentProduct->getConfigurablePrice();

                    if (isset($options[$attributeId][$value['value_index']])) {
                        $productsIndex = $options[$attributeId][$value['value_index']];
                    } else {
                        $productsIndex = array();
                    }

                    $info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_prepareOldPrice($value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
                        'image'     => isset($optionImages[$value['value_index']]) ? $optionImages[$value['value_index']] : null // PATCH
                    );
                    $optionPrices[] = $configurablePrice;
                }
            }
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
                $attributes[$attributeId] = $info;
            }

            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
                if ($configValue) {
                    $defaultValues[$attributeId] = $configValue;
                }
            }
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $_request = $taxCalculation->getRateRequest(false, false, false);
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $defaultTax = $taxCalculation->getRate($_request);

        $_request = $taxCalculation->getRateRequest();
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $currentTax = $taxCalculation->getRate($_request);

        $taxConfig = array(
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax')
        );

        $config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig
        );

        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        $config = array_merge(
            $config,
            array(
                'origins'   => $origins,
                'images'    => $images,
                'thumbs'    => $thumbs,
                'show'      => Mage::getStoreConfig('mtattribute/general/show'),
                'imgWidth'  => is_numeric(Mage::getStoreConfig('mtattribute/general/width'))?Mage::getStoreConfig('mtattribute/general/width'):50,
                'czoomRel' => Mage::helper('mtattribute')->getCloudZoomConfig(true)
            ),
            $this->_getAdditionalConfig()
        );

        return Mage::helper('core')->jsonEncode($config);
    }

    protected function _prepareOldPrice($price, $isPercent=false){
        if (version_compare(Mage::getVersion(), '1.7.0.0') < 0){
            return $this->_preparePrice($price, $isPercent);
        }else{
            return parent::_prepareOldPrice($price, $isPercent);
        }
    }
}