<?php

class Magehit_Faq_Adminhtml_FaqController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('faq/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('faq/faq')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('faq_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('faq/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('faq/adminhtml_faq_edit'))
				->_addLeft($this->getLayout()->createBlock('faq/adminhtml_faq_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
		if (!is_numeric($this->getRequest()->getParam('faq_order'))) {
					 Mage::getSingleton('adminhtml/session')->setFormData($data);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('The FAQ order must be in digital format'));
				   $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				};
		$faq = Mage::getModel('faq/faq')->getCollection()
		->addFieldToFilter('faqname',array('like'=>$this->getRequest()->getParam('faqname')))
		;
			if($this->getRequest()->getParam('id')==null)
			if (count($faq)>=1){
					 Mage::getSingleton('adminhtml/session')->setFormData($data);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('The FAQ category name has exited'));
				   $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
			};
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('faq/faq');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				
				if(isset($data['stores'])){
					$stores = $data['stores'];
				}else{
					$stores = array(null);
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('faq')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('faq/faq');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $faqIds = $this->getRequest()->getParam('faq');
        if(!is_array($faqIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    $faq = Mage::getModel('faq/faq')->load($faqId);
                    $faq->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($faqIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $faqIds = $this->getRequest()->getParam('faq');
        if(!is_array($faqIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    $faq = Mage::getSingleton('faq/faq')
                        ->load($faqId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($faqIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'faq.csv';
        $content    = $this->getLayout()->createBlock('faq/adminhtml_faq_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'faq.xml';
        $content    = $this->getLayout()->createBlock('faq/adminhtml_faq_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}