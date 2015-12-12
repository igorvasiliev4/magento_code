<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Attribute_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute{
    /**
     * Get data array for building attribute filter items
     * Override to replace label by attribute option image
     *
     * @return array
     * @version 1.7.0.2
     */
    protected function _getItemsData()
    {
        if (!Mage::getStoreConfig('mtattribute/general/show3')) return parent::_getItemsData();
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $options = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);

            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->getAttributeId())
                ->setStoreFilter()
                ->load();

            $width = Mage::getStoreConfig('mtattribute/general/width3');
            $width = $width ? $width : 30;
            $thumbs = array();
            foreach($optionCollection as $opt){
                if ($opt->getThumb()){
                    $thumbs[$attribute->getAttributeId()][$opt->getOptionId()] = sprintf(
                        '<img src="%s" width="%d" title="%s" style="margin-bottom: 2px;border: 2px solid #ccc"/>',
                        $opt->getThumb(),
                        $width,
                        $opt->getValue()
                    );
                }
            }

            $data = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => isset($thumbs[$attribute->getAttributeId()][$option['value']]) ? $thumbs[$attribute->getAttributeId()][$option['value']] : $option['label'],
                                'value' => $option['value'],
                                'count' => $optionsCount[$option['value']],
                            );
                        }
                    }
                    else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        );
                    }
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
}