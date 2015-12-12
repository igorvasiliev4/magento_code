<?php
/**
 * @category    Fishpig
 * @package     Fishpig_iBanners
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_iBanners_Model_System_Config_Source_Carousel_Transition
{
	/**
	 * Retrieve an array of possible options
	 *
	 * @return array
	 */
	public function toOptionArray($includeEmpty = false, $emptyText = '-- Please Select --')
	{
		$options = array();
		
		if ($includeEmpty) {
			$options[] = array(
				'value' => '',
				'label' => Mage::helper('adminhtml')->__($emptyText),
			);
		}
		
		foreach($this->getOptions() as $value => $label) {
			$options[] = array(
				'value' => $value,
				'label' => Mage::helper('adminhtml')->__($label),
			);
		}
	
		return $options;
	}
	
	/**
	 * Retrieve an array of possible options
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return array(
			'sinoidal' => 'Sinoidal',
			'spring' => 'Spring',
		);
	}
}
