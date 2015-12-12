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

class MagenThemes_MTGhost_Helper_Cssgen extends Mage_Core_Helper_Abstract
{
	/**
	 * Path and directory of the automatically generated CSS
	 *
	 * @var string
	 */
	protected $_generatedCssFolder;
	protected $_generatedCssPath;
	protected $_generatedCssDir;
	
	public function __construct()
	{
		//Create paths
		$this->_generatedCssFolder = 'css/_config/';
		$this->_generatedCssPath = 'frontend/mtghost/default/' . $this->_generatedCssFolder;
		$this->_generatedCssDir = Mage::getBaseDir('skin') . '/' . $this->_generatedCssPath;
	}
	
	/**
	 * Get automatically generated CSS directory
	 *
	 * @return string
	 */
	public function getGeneratedCssDir()
    {
        return $this->_generatedCssDir;
    }

	/**
	 * Get file path: CSS design
	 *
	 * @return string
	 */
	public function getDesignFile()
	{
		return $this->_generatedCssFolder . 'design_' . Mage::app()->getStore()->getCode() . '.less';
	}

    /**
     * Get file path: CSS layout
     *
     * @return string
     */
    public function getLayoutFile()
    {
        return $this->_generatedCssFolder . 'layout_' . Mage::app()->getStore()->getCode() . '.css';
    }
}
