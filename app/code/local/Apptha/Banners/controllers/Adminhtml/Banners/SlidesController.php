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

class Apptha_Banners_Adminhtml_Banners_SlidesController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Redirect to Banners dashboard
	 *
	 * @return $this
	 */
	public function indexAction()
	{
		return $this->_redirect('*/banners');
	}
	
 	/**
     * Get related banner grid and serializer block
     */
    public function bannerGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
	/**
     * Generate the block code for the sider
     */
    public function getcodeAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	/**
	 * Forward to the edit action so the user can add a new group
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
		$group = $this->_initSliderModel();
		
		$this->loadLayout();
		$this->_title('Banners');
		$this->renderLayout();
	}
	
	/**
	 * Save the group
	 *
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('slides')) {
			$slide = Mage::getModel('banners/slides')
				->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				$slide->save();
				$this->_getSession()->addSuccess($this->__('Banner slides was saved'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				Mage::logException($e);
			}
			
			if ($this->getRequest()->getParam('back') && $slide->getId()) {
				$this->_redirect('*/*/edit', array('id' => $slide->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save'));
		}
		
		$this->_redirect('*/banners');
	}
	
	/**
	 * Delete a ibanners group
	 *
	 */
	public function deleteAction()
	{
		if ($groupId = $this->getRequest()->getParam('id')) {
			$group = Mage::getModel('banners/slides')->load($groupId);
			
			if ($group->getId()) {
				try {
					$group->delete();
					$this->_getSession()->addSuccess($this->__('The banner group was deleted.'));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/banners');
	}
	
	/**
	 * Delete multiple ibanners groups in one go
	 *
	 */
	public function massDeleteAction()
	{
		$groupIds = $this->getRequest()->getParam('slides');

		if (!is_array($groupIds)) {
			$this->_getSession()->addError($this->__('Please select some groups.'));
		}
		else {
			if (!empty($groupIds)) {
				try {
					foreach ($groupIds as $groupId) {
						$group = Mage::getSingleton('banners/slides')->load($groupId);
	
						Mage::dispatchEvent('banners_controller_slides_delete', array('banners_slides' => $group));
	
						$group->delete();
					}
					
					$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($groupIds)));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/banners');
	}
	
	/**
	 * Initialise the slider model
	 *
	 * @return null|Apptha_Banners_Model_Slides
	 */
	protected function _initSliderModel()
	{
		if ($groupId = $this->getRequest()->getParam('id')) {
			$group = Mage::getModel('banners/slides')->load($groupId);
			
			if ($group->getId()) {
				Mage::register('banners_slides', $group);
			}
		}
		
		return Mage::registry('banners_slides');
	}
}