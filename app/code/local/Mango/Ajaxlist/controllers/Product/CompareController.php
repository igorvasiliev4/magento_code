<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Catalog comapare controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
//require_once 'Mage/Catalog/controllers/Product/CompareController.php';
require_once Mage::getModuleDir('controllers', 'Mage_Catalog') . DS . 'Product' . DS . 'AccountController.php';
class Mango_Ajaxlist_Product_CompareController extends Mage_Catalog_Product_CompareController {
    public function preDispatch() {
        parent::preDispatch();
        $this->getRequest()->setRouteName('catalog');
    }
    protected function _redirectReferer($defaultUrl = null) {
        $refererUrl = $this->_getRefererUrl();
        if (empty($refererUrl)) {
            $refererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
        }
        $_ajaxparameter = Mage::helper("ajaxlist")->getAjaxParameter();
        $pos = strpos($refererUrl, $_ajaxparameter. "=1");
        $_ajaxparameter = Mage::helper("ajaxlist")->getAjaxParameter();
        $isAjax = $this->getRequest()->getParam($_ajaxparameter);
        if ($pos === false && $isAjax) {
            $pos = strpos($refererUrl, "?");
            if ($pos === false) {
                $this->getResponse()->setRedirect($refererUrl . "?ajax=1");
            } else {
                $this->getResponse()->setRedirect($refererUrl . "&ajax=1");
            }
        } else {
            $this->getResponse()->setRedirect($refererUrl);
        }
        return $this;
    }
}
