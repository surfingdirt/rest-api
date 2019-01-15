<?php

class Lib_Controller_Plugin_RequestHandler extends Zend_Controller_Plugin_Abstract
{
  public function preDispatch($request)
  {
    if (!$request instanceof Zend_Controller_Request_Http) {
      return;
    }

    if ($request->isPut() || $request->isPost()) {
      $jsonParams = Zend_Json::decode($request->getRawBody());
      $request->setParams($jsonParams);
    };
  }
}
