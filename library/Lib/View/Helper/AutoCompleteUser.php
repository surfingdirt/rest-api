<?php
class Lib_View_Helper_AutoCompleteUser extends Lib_View_Helper_AutoCompleteData
{
    protected $_route = 'getperson';

    public function autoCompleteUser($id, $value = null, $params = array(), $attribs = array())
    {
        $params['scrollHeight'] = "12em";
        return $this->_autoComplete($id, $value, $params, $attribs);
    }
}