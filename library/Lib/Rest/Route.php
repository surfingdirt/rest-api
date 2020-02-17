<?php
class Lib_Rest_Route extends Zend_Controller_Router_Route
{
  public function match($request, $partial = false)
  {
    $result = parent::match($request, $partial);
    $requestMethod = strtolower($request->getMethod());
    $result['action'] = $requestMethod;
    $result['controller'] = $this->_defaults['controller'];
    return $result;
  }
}