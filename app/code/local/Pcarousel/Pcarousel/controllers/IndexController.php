<?php
class Pcarousel_Pcarousel_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/pcarousel?id=15 
    	 *  or
    	 * http://site.com/pcarousel/id/15 	
    	 */
    	/* 
		$pcarousel_id = $this->getRequest()->getParam('id');

  		if($pcarousel_id != null && $pcarousel_id != '')	{
			$pcarousel = Mage::getModel('pcarousel/pcarousel')->load($pcarousel_id)->getData();
		} else {
			$pcarousel = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($pcarousel == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$pcarouselTable = $resource->getTableName('pcarousel');
			
			$select = $read->select()
			   ->from($pcarouselTable,array('pcarousel_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$pcarousel = $read->fetchRow($select);
		}
		Mage::register('pcarousel', $pcarousel);
		*/
        
			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function aaction()
    {
        
        $this->loadLayout();     
		$this->renderLayout();
    }
}