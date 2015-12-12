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
class MagenThemes_MTTheme_Model_System_Layout_Observer
{
    const ROOTNODE = 'root';
    public function unremoveUpdate($observer)
    {
        $layout = $observer->getLayout();
        $update = $observer->getLayout()->getUpdate();
        $original_updates = $update->asArray();
        $update->resetUpdates();

        $to_unremove  = $this->_getUnremoveNames($this->_getSimplexmlFromFragment(implode('',$original_updates)));

        foreach($original_updates as $s_xml_update)
        {
            $s_xml_update = $this->_processUnremoveNodes($s_xml_update, $to_unremove);
            $update->addUpdate($s_xml_update);
        }
    }

    protected function _processUnremoveNodes($string, $to_unremove)
    {
        $o_xml_update = $this->_getSimplexmlFromFragment($string);
        $nodes = $o_xml_update->xpath('//remove');
        foreach($nodes as $node)
        {
            if(in_array($node['name'], $to_unremove))
            {
                unset($node['name']);
            }
        }

        $s_xml = '';
        foreach($o_xml_update->children() as $node)
        {
            $s_xml .= $node->asXml();
        }
        return $s_xml;

    }

    protected function _getUnremoveNames($xml)
    {
        $nodes 		= $xml->xpath('//unremove');
        $unremove 	= array();
        foreach($nodes as $node)
        {
            $unremove[] = (string) $node['name'];
        }
        return $unremove;
    }

    protected function _getSimplexmlFromFragment($fragment)
    {
        return simplexml_load_string('<'.self::ROOTNODE.'>'.$fragment.'</'.self::ROOTNODE.'>');
    }
}