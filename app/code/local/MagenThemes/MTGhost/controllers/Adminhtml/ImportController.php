<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package Mato Framework
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2015 ZooExtension.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       ZooExtension.com
 * @email        magento@cleversoft.co
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class MagenThemes_MTGhost_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/mtghost/"));
    }
    public function blocksAction() {
        $config = Mage::helper('mtghost')->getCfg('install/overwrite_blocks');
        Mage::getSingleton('mtghost/import_cms')->importCmsItems('cms/block', 'blocks', $config);
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/mtghost/"));
    }
    public function pagesAction() {
        $config = Mage::helper('mtghost')->getCfg('install/overwrite_pages');
        Mage::getSingleton('mtghost/import_cms')->importCmsItems('cms/page', 'pages', $config);
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/mtghost/"));
    }
    public function widgetsAction() {
        Mage::getSingleton('mtghost/import_cms')->importWidgetItems('widget/widget_instance', 'widgets', false);
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/mtghost/"));
    }
}
