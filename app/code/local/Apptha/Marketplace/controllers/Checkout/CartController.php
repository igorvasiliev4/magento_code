<?php
/*** Apptha** NOTICE OF LICENSE** This source file is subject to the EULA* that is bundled with this package in the file LICENSE.txt.* It is also available through the world-wide-web at this URL:* http://www.apptha.com/LICENSE.txt** ==============================================================*                 MAGENTO EDITION USAGE NOTICE* ==============================================================* This package designed for Magento COMMUNITY edition* Apptha does not guarantee correct work of this extension* on any other Magento edition except Magento COMMUNITY edition.* Apptha does not provide extension support in case of* incorrect edition usage.* ==============================================================** @category    Apptha* @package     Apptha_Marketplace* @version     0.1.7* @author      Apptha Team <developers@contus.in>* @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)* @license     http://www.apptha.com/LICENSE.txt**/
?><?php
/**
 * Shopping cart controller
 */
require_once 'Mage/Checkout/controllers/CartController.php';
class Apptha_Marketplace_Checkout_CartController extends Mage_Checkout_CartController {
	/**
	 * Delete shoping cart item action
	 */
	public function deleteAction() {
		$id = ( int ) $this->getRequest ()->getParam ( 'id' );
		
		if ($id) {
			
			try {
				
				$this->_getCart ()->removeItem ( $id )->

				save ();
			} catch ( Exception $e ) {
				
				$this->_getSession ()->addError ( $this->__ ( 'Cannot remove the item.' ) );
				
				Mage::logException ( $e );
			}
		}
		
		$this->_redirectUrl ( $_SERVER ['HTTP_REFERER'] );
	}
	
	/**
	 *
	 *
	 * Add product to shopping cart action
	 *
	 *
	 *
	 * @return Mage_Core_Controller_Varien_Action
	 *
	 * @throws Exception
	 *
	 */
	public function addAction() 

	{
		if (! $this->_validateFormKey ()) {
			
			$this->_goBack ();
			
			return;
		}
		
		$cart = $this->_getCart ();
		
		$params = $this->getRequest ()->getParams ();
		
		$productId = $this->getRequest ()->getParam ( 'product' );
		
		$customer = Mage::getSingleton ( 'customer/session' )->isLoggedIn ();
		
		$customerId = Mage::getSingleton ( 'customer/session' )->getId ();
		
		$sellerDetails = Mage::getModel ( 'marketplace/sellerprofile' )->load ( $customerId, 'seller_id' )->getData ();
		
		$gid = Mage::helper ( 'marketplace' )->getGroupId ();
		
		/**
		 * Get Customer Collection
		 */
		
		$collection = Mage::getResourceModel ( 'customer/customer_collection' )->

		addNameToSelect ()->

		addAttributeToSelect ( 'email' )->

		addAttributeToSelect ( 'created_at' )->addAttributeToSelect ( 'group_id' )->

		addAttributeToSelect ( 'customerstatus' )->

		addFieldToFilter ( 'group_id', $gid )->

		addFieldToFilter ( 'entity_id', $customerId );
		
		if (empty ( $collection->getData () )) {
			
			try {
				
				$sellerDetails = Mage::getModel ( 'marketplace/sellerprofile' )->load ( $cartSellerId, 'seller_id' );
				
				$sellerClientId = $sellerDetails->getMeraClientId ();
				
				$sellerClientSecret = $sellerDetails->getClientSecret ();
				
				if (isset ( $params ['qty'] )) {
					
					$filter = new Zend_Filter_LocalizedToNormalized ( 

					array (
							'locale' => Mage::app ()->getLocale ()->getLocaleCode () 
					) )

					;
					
					$params ['qty'] = $filter->filter ( $params ['qty'] );
				}
				
				$product = $this->_initProduct ();
				
				$related = $this->getRequest ()->getParam ( 'related_product' );
				
				/**
				 * Check product availability
				 */
				
				if (! $product) {
					
					$this->_goBack ();
					
					return;
				}
				
				$cart->addProduct ( $product, $params );
				
				if (! empty ( $related )) {
					
					$cart->addProductsByIds ( explode ( ',', $related ) );
				}
				
				$cart->save ();
				
				$this->_getSession ()->setCartWasUpdated ( true );
				
				Mage::dispatchEvent ( 'checkout_cart_add_product_complete', 

				array (
						'product' => $product,
						'request' => $this->getRequest (),
						'response' => $this->getResponse () 
				) )

				;
				
				if (! $this->_getSession ()->getNoCartRedirect ( true )) {
					
					if (! $cart->getQuote ()->getHasError ()) {
						
						$message = $this->__ ( '%s was added to your shopping cart.', Mage::helper ( 'core' )->escapeHtml ( $product->getName () ) );
						
						$this->_getSession ()->addSuccess ( $message );
					}
					
					$this->_goBack ();
				}
			} catch ( Mage_Core_Exception $e ) {
				
				if ($this->_getSession ()->getUseNotice ( true )) {
					
					$this->_getSession ()->addNotice ( Mage::helper ( 'core' )->escapeHtml ( $e->getMessage () ) );
				} else {
					
					$messages = array_unique ( explode ( "\n", $e->getMessage () ) );
					
					foreach ( $messages as $message ) {
						
						$this->_getSession ()->addError ( Mage::helper ( 'core' )->escapeHtml ( $message ) );
					}
				}
				
				$url = $this->_getSession ()->getRedirectUrl ( true );
				
				if ($url) {
					
					$this->getResponse ()->setRedirect ( $url );
				} else {
					
					$this->_redirectReferer ( Mage::helper ( 'checkout/cart' )->getCartUrl () );
				}
			} catch ( Exception $e ) {
				
				$this->_getSession ()->addException ( $e, $this->__ ( 'Cannot add the item to shopping cart.' ) );
				
				Mage::logException ( $e );
				
				$this->_goBack ();
			}
		} else {
			
			$message = $this->__ ( 'You cannot able to purchase from your seller account.' );
			
			$this->_getSession ()->addError ( $message );
			
			$this->_redirectUrl ( $_SERVER ['HTTP_REFERER'] );
		}
	}
}