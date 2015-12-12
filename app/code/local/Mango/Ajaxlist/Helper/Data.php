<?php

class Mango_Ajaxlist_Helper_Data extends Mage_Core_Helper_Abstract{

    const  _AJAX_PARAMETER = 'ajaxlist';

    public function getAjaxParameter(){
        return $this::_AJAX_PARAMETER;
    }

    public function removeAjaxParameters( &$_params ){
        $_params[ $this::_AJAX_PARAMETER ] = null;
        return $_params;
    }

}