<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category Mato
 * @package      MT_AjaxCart
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
require_once 'Mage/Checkout/controllers/CartController.php';
class MagenThemes_AjaxCart_IndexController extends Mage_Checkout_CartController
{
	/**
	* 
	* Add to cart product action
	* Return product
	*/
	public function addAction()
	{
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		if($params['isAjax'] == 1){
			$response = array();
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct(); 
				$related = $this->getRequest()->getParam('related_product');
				
				if (!$product) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$productarray = array_unique(explode(',', $related));
					$cart->addProductsByIds($productarray);
				}

				$cart->save();

				$this->_getSession()->setCartWasUpdated(true); 
				
				Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$cart->getQuote()->getHasError()){
					$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
					$response['status'] = 'SUCCESS';
					$response['message'] = $message;
					//New Code Here
					$this->loadLayout(); 
					$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
					$output = $this->getLayout()->getBlock('ajaxcart')->toHtml();
					$this->getResponse()->setBody($output); 
					Mage::register('referrer_url', $this->_getRefererUrl()); 
                    $response['output'] = $output;
                    $title = $this->__('%d X %s was added to your shopping cart.', $params['qty'], Mage::helper('core')->htmlEscape($product->getName()));
                    $response['product_info'] = array(
                        'image_url' => (string)Mage::helper('mtghost/image')->getImg($product, 100, 100, 'small_image'),
                        'title' => $title
                    );
					$response['toplink'] = $toplink;
				}
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message.'<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				Mage::logException($e);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}else{
			return parent::addAction();
		}
	}
	
	/**
	* 
	* Add to cart product options, quickview
	*  
	*/
	public function optionsAction(){
		$productId = $this->getRequest()->getParam('product_id'); 
		
		$viewHelper = Mage::helper('catalog/product_view');
	
		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);
		
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}
	
	/**
	* 
	* Delete product add to cart
	*  
	*/
	public function deleteAction()
	{
		$id = (int) $this->getRequest()->getParam('id');  
		$response = array();
		if ($id) {
			try {  
				$this->_getCart()->removeItem($id)
					->save();  
				$this->loadLayout(); 
				$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
				$output = $this->getLayout()->getBlock('ajaxcart')->toHtml();
				$this->getResponse()->setBody($output);
				Mage::register('referrer_url', $this->_getRefererUrl());
				$message = $this->__('Product removed to your shopping cart.');
				$response['status'] = 'SUCCESS';
				$response['message'] = $message;
				$response['output'] = $output; 
				$response['toplink'] = $toplink;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->_getSession()->addError($this->__('Cannot remove the item.')); 
				Mage::logException($e);
			}
		} 
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 
	}
	
	/**
	* 
	* Update product edit add to cart
	*  
	*/
	public function updateItemOptionsAction()
	{
		$cart   = $this->_getCart();
		$id = (int) $this->getRequest()->getParam('id');
		$params = $this->getRequest()->getParams();
		$response = array();
		if (!isset($params['options'])) {
			$params['options'] = array();
		}
		try {
			if (isset($params['qty'])) {
				$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
				);
				$params['qty'] = $filter->filter($params['qty']);
			}
	
			$quoteItem = $cart->getQuote()->getItemById($id);
			if (!$quoteItem) {
				Mage::throwException($this->__('Quote item is not found.'));
			}
	
			$item = $cart->updateItem($id, new Varien_Object($params));
			if (is_string($item)) {
				Mage::throwException($item);
			}
			if ($item->getHasError()) {
				Mage::throwException($item->getMessage());
			}
	
			$related = $this->getRequest()->getParam('related_product');
			if (!empty($related)) {
				$cart->addProductsByIds(explode(',', $related));
			}
	
			$cart->save();
	
			$this->_getSession()->setCartWasUpdated(true);
	
			Mage::dispatchEvent('checkout_cart_update_item_complete',
			array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
			);
			if (!$this->_getSession()->getNoCartRedirect(true)) {
				if (!$cart->getQuote()->getHasError()){
					$message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
					$this->_getSession()->addSuccess($message);
					$response['status'] = 'SUCCESS';
					$response['message'] = $message;
					//New Code Here
					$this->loadLayout();
					$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
					$output = $this->getLayout()->getBlock('ajaxcart')->toHtml();
					$this->getResponse()->setBody($output);
					Mage::register('referrer_url', $this->_getRefererUrl());
					$response['output'] = $output;
					$response['toplink'] = $toplink;
				}
			}
		} catch (Exception $e) {
			$response['status'] = 'ERROR';
	
			$response['message'] = $this->_getSession()->addError($this->__('Cannot update the item.'));
	
			Mage::logException($e);
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	}
	
	/**
	* 
	* Add Compare Action
	* Return block
	*/
	public function compareAction(){
		$response = array(); 
		if ($productId = (int) $this->getRequest()->getParam('product')) {
			$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId); 
			if ($product->getId()/* && !$product->isSuper()*/) {
				Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
				$response['status'] = 'SUCCESS';
				$response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
				Mage::register('referrer_url', $this->_getRefererUrl());
				Mage::helper('catalog/product_compare')->calculate();
				Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
				$this->loadLayout();
				$header_compare = $this->getLayout()->getBlock('compareajax')->toHtml();
				$this->getResponse()->setBody($header_compare);  
				$sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
				$sidebar_block->setTemplate('catalog/product/compare/sidebar.phtml');
				$sidebar = $sidebar_block->toHtml();
				$response['output'] = $sidebar;
				$response['topcompare'] = $header_compare; 
			}
		}
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
		return;
	}
	
	/**
	* 
	* Remove Compare Action
	* Return block
	*/
	public function rmcompareAction()
	{
		$response = array();
		if ($productId = (int) $this->getRequest()->getParam('product')) {
			$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId); 
			if($product->getId()) { 
				$item = Mage::getModel('catalog/product_compare_item'); 
				if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }
				$item->loadByProduct($product); 
				if($item->getId()) {
					$item->delete();
					$response['status'] = 'SUCCESS';
					$response['message'] = $this->__('The product %s has been removed from comparison list.', $product->getName()); 
					Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
					Mage::helper('catalog/product_compare')->calculate();
					$this->loadLayout();
					$header_compare = $this->getLayout()->getBlock('compareajax'); 
					$header_compare->setTemplate('catalog/product/compare/header.phtml');
					$blockcompare = $header_compare->toHtml();
					$sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
					$sidebar_block->setTemplate('catalog/product/compare/sidebar.phtml');
					$sidebar = $sidebar_block->toHtml();
					$response['output'] = $sidebar;
					$response['topcompare'] = $blockcompare; 
				}
			}
		} 
		$this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
		return;  
	}
	
	/**
	* 
	* Clear All Compare Action
	* Return block
	*/
	public function clearallAction(){
        $response = array();
        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $session->addSuccess($this->__('The comparison list was cleared.'));
            $response['status'] = 'SUCCESS';
            $response['message'] = $this->__('The comparison list was cleared.');
            Mage::helper('catalog/product_compare')->calculate();
            $this->loadLayout();
            $header_compare = $this->getLayout()->getBlock('compareajax'); 
			$header_compare->setTemplate('catalog/product/compare/header.phtml');
			$blockcompare = $header_compare->toHtml();
            $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
            $sidebar_block->setTemplate('catalog/product/compare/sidebar.phtml');
            $sidebar = $sidebar_block->toHtml();
            $response['output'] = $sidebar;
            $response['topcompare'] = $blockcompare; 
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while clearing comparison list.'));
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
        return;
    }
	
	/**
	* 
	* Load Wishlist
	* Return Wishlist
	*/
	protected function _getWishlist($wishlistId = null)
	{
		$wishlist = Mage::registry('wishlist');
		if ($wishlist) {
			return $wishlist;
		} 
		try {
			if (!$wishlistId) {
				$wishlistId = $this->getRequest()->getParam('wishlist_id');
			}
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
			/* @var Mage_Wishlist_Model_Wishlist $wishlist */
			$wishlist = Mage::getModel('wishlist/wishlist');
			if ($wishlistId) {
				$wishlist->load($wishlistId);
			} else {
				$wishlist->loadByCustomer($customerId, true);
			}
	
			if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
				$wishlist = null;
				Mage::throwException(
				Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
				);
			}
	
			Mage::register('wishlist', $wishlist);
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
			return false;
		} catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addException($e,
			Mage::helper('wishlist')->__('Wishlist could not be created.')
			);
			return false;
		}
	
		return $wishlist;
	}
	
	/**
	* 
	* Add Wishlist Action
	* Return block
	*/
	public function wishlistAction()
	{ 
		$response = array();
		if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
		}
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Please Login First');
		}

		if(empty($response)){
			$session = Mage::getSingleton('customer/session');
			$wishlist = $this->_getWishlist();
			if (!$wishlist) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Unable to Create Wishlist');
			}else{

				$productId = (int) $this->getRequest()->getParam('product');
				if (!$productId) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Product Not Found');
				}else{

					$product = Mage::getModel('catalog/product')->load($productId);
					if (!$product->getId() || !$product->isVisibleInCatalog()) {
						$response['status'] = 'ERROR';
						$response['message'] = $this->__('Cannot specify product.');
					}else{ 
						try {
							$requestParams = $this->getRequest()->getParams();
							$buyRequest = new Varien_Object($requestParams);

							$result = $wishlist->addNewItem($product, $buyRequest);
							if (is_string($result)) {
								Mage::throwException($result);
							}
							$wishlist->save();

							Mage::dispatchEvent(
                				'wishlist_add_product',
							array(
			                    'wishlist'  => $wishlist,
			                    'product'   => $product,
			                    'item'      => $result
							)
							);

							Mage::helper('wishlist')->calculate();

							$message = $this->__('%1$s has been added to your wishlist.', $product->getName());
							$response['status'] = 'SUCCESS';
							$response['message'] = $message; 
							Mage::unregister('wishlist'); 
							$this->loadLayout();
							$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
							$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar'); 
							if($sidebar_block){
								$sidebar_block->setTemplate('wishlist/sidebar.phtml');
								$sidebar = $sidebar_block->toHtml();  
								$response['sidebar'] = $sidebar;
							} 
							$response['toplink'] = $toplink;
						}
						catch (Mage_Core_Exception $e) {
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
						}
						catch (Exception $e) {
							mage::log($e->getMessage());
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist.');
						}
					}
				}
			}

		}

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
		return;
	}

    /**
     * Action to accept new configuration for a wishlist item
     */
    public function updateWishlistAction()
    {
        $response = array();
        $session = Mage::getSingleton('customer/session');
        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Product Not Found');
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Cannot specify product.');
        }

        try {
            $id = (int) $this->getRequest()->getParam('id');
            /* @var Mage_Wishlist_Model_Item */
            $item = Mage::getModel('wishlist/item');
            $item->load($id);
            $wishlist = $this->_getWishlist($item->getWishlistId());
            if (!$wishlist) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Unable to update Wishlist');
            }

            $buyRequest = new Varien_Object($this->getRequest()->getParams());

            $wishlist->updateItem($id, $buyRequest)
                ->save();

            Mage::helper('wishlist')->calculate();
            Mage::dispatchEvent('wishlist_update_item', array(
                    'wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($id))
            );

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been updated in your wishlist.', $product->getName());
            $response['status'] = 'SUCCESS';
            $response['message'] = $message;
            $this->loadLayout();
            $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
            $sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
            if($sidebar_block){
                $sidebar_block->setTemplate('wishlist/sidebar.phtml');
                $sidebar = $sidebar_block->toHtml();
                $response['sidebar'] = $sidebar;
            }
            $response['toplink'] = $toplink;
        } catch (Mage_Core_Exception $e) {
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('An error occurred while updating wishlist.');
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
    }
	
	/**
	* 
	* Remove Wishlist Action
	* Return block
	*/
	public function removeAction()
	{
		$response = array();	
		$id = (int) $this->getRequest()->getParam('item');
		$item = Mage::getModel('wishlist/item')->load($id);
		if (!$item->getId()) {
			return $this->norouteAction();
		}
		$wishlist = $this->_getWishlist($item->getWishlistId());
		if (!$wishlist) {
			return $this->norouteAction();
		}
		try {
			$item->delete();
			$wishlist->save();
			$response['status'] = 'SUCCESS';
			$response['message'] = $this->__('The product %s has been removed from Wishlist.', $item->getName());
			$this->loadLayout();
			$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
			$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
			$sidebar_block->setTemplate('wishlist/sidebar.phtml');
			$sidebar = $sidebar_block->toHtml();
			$response['toplink'] = $toplink;
			$response['sidebar'] = $sidebar;
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('customer/session')->addError(
			$this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
			);
		} catch(Exception $e) {
			Mage::getSingleton('customer/session')->addError(
			$this->__('An error occurred while deleting the item from wishlist.')
			);
		} 
		Mage::helper('wishlist')->calculate(); 
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
		return;
	}
	
}