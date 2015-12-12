<?php
/**
* Apptha
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://www.apptha.com/LICENSE.txt
*
* ==============================================================
*                 MAGENTO EDITION USAGE NOTICE
* ==============================================================
* This package designed for Magento COMMUNITY edition
* Apptha does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* Apptha does not provide extension support in case of
* incorrect edition usage.
* ==============================================================
*
* @category    Apptha
* @package     Apptha_Banners
* @version     0.1.0
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
*
*/

class Apptha_Banners_Adminhtml_Banners_BannerController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Redirect to iBanners dashboard
	 *
	 * @return $this
	 */
	public function indexAction()
	{
		return $this->_redirect('*/banners');
	}

	/**
	 * Forward to the edit action so the user can add a new banner
	 *
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	/**
	 * Display the edit/add form
	 *
	 */
	public function editAction()
	{
		$this->loadLayout();
		
		$this->_title('Banners');

		if ($banner = $this->_initBannerModel()) {
			$this->_title($banner->getTitle());
		}

		$this->renderLayout();
	}
	
	/**
	 * Save the banner
	 *
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('banner')) {
			$banner = Mage::getModel('banners/banner')
				->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				
				$this->_handleImageUpload($banner);
				
				$banner->save();
				
				Mage::dispatchEvent('banners_controller_banner_added', array('banner' => $banner,'slide'=>$data['slideids']));
				
				$this->_getSession()->addSuccess($this->__('Banner was saved'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				Mage::logException($e);
			}
			
			if ($this->getRequest()->getParam('back') && $banner->getId()) {
				$this->_redirect('*/*/edit', array('id' => $banner->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save'));
		}
		
		$this->_redirect('*/banners');
	}

	/**
	 * Upload an image and assign it to the model
	 *
	 * @param Apptha_Banners_Model_Banner $banner
	 * @param string $field = 'image'
	 */
	protected function _handleImageUpload(Apptha_Banners_Model_Banner $banner, $field = 'image')
	{
		$data = $banner->getData($field);

		if (isset($data['value'])) {
			$banner->setData($field, $data['value']);
		}

		if (isset($data['delete']) && $data['delete'] == '1') {
			$banner->setData($field, '');
		}

		if ($filename = Mage::helper('banners/image')->uploadImage($field)) {
			$banner->setData($field, $filename);
		}
	}
	
	/**
	 * Delete a banners banner
	 *
	 */
	public function deleteAction()
	{
		if ($bannerId = $this->getRequest()->getParam('id')) {
			$banner = Mage::getModel('banners/banner')->load($bannerId);
			
			if ($banner->getId()) {
				try {
					$this->removeBannerFromSlider($banner->getId());
					$banner->delete();
					$this->_getSession()->addSuccess($this->__('The banner was deleted.'));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/banners');
	}
	
	/**
	 * Delete multiple banners banners in one go
	 *
	 */
	public function massDeleteAction()
	{
		$bannerIds = $this->getRequest()->getParam('banner');

		if (!is_array($bannerIds)) {
			$this->_getSession()->addError($this->__('Please select some banners.'));
		}
		else {
			if (!empty($bannerIds)) {
				try {
					foreach ($bannerIds as $bannerId) {
						$banner = Mage::getSingleton('banners/banner')->load($bannerId);
	
						Mage::dispatchEvent('banners_controller_banner_delete', array('banners_banner' => $banner));
						$this->removeBannerFromSlider($banner->getId());
						$banner->delete();
					}
					
					$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($bannerIds)));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/banners');
	}
	
	/**
	 * Initialise the banner model
	 *
	 * @return null|Apptha_Banners_Model_Banner
	 */
	protected function _initBannerModel()
	{
		if ($bannerId = $this->getRequest()->getParam('id')) {
			$banner = Mage::getModel('banners/banner')->load($bannerId);
			
			if ($banner->getId()) {
				Mage::register('banners_banner', $banner);
			}
		}
		
		return Mage::registry('banners_banner');
	}
	
	/**
	 * Initialise the banner model
	 *
	 * @param int $bannerId 
	 * @return void
	 */
	protected function removeBannerFromSlider($bannerId)
	{
		Mage::getModel('banners/bannerSlide')->getCollection()->getValueByBanner($bannerId)->delete();
		
		return ;
	}
	
	
}