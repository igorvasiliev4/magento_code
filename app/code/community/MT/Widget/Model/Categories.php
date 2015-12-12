<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author ZooExtension.com
 * @email       magento@cleversoft.co
 */
class MT_Widget_Model_Categories {
    protected  $_catTree = '';

    protected function nodeToArray(Varien_Data_Tree_Node $node) {
        $result = array();
        $result['category_id'] = $node->getId();
        $result['parent_id'] = $node->getParentId();
        $result['name'] = $node->getName();
        $result['is_active'] = $node->getIsActive();
        $result['position'] = $node->getPosition();
        $result['level'] = $node->getLevel();
        $result['children'] = array();

        foreach ($node->getChildren() as $child) {
            $result['children'][] = $this->nodeToArray($child);
        }

        return $result;
    }

    public function load_tree() {
        $store = 1;
        $parentId = 1;
        $tree = Mage::getResourceSingleton('catalog/category_tree')->load();
        $root = $tree->getNodeById($parentId);

        if($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($store)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        return $this->nodeToArray($root);
    }

    public function render_tree($tree,$level) {
        foreach($tree as $item) {
            $this->_catTree[]=array('value' => $item['category_id'],'label' => str_repeat("_", $level).$item['name']);
            $this->render_tree($item['children'],$level+1);
        }
    }

    public function toOptionArray() {
        $tree = $this->load_tree();
        $this->render_tree($tree['children'],0);
        return $this->_catTree;
    }
}