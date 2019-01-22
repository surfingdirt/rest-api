<?php

class Lib_Controller_Plugin_RequestHandler extends Zend_Controller_Plugin_Abstract
{
  const APPLICATION_JSON = 'application/json';

  public function preDispatch($request)
  {
    if (!$request instanceof Zend_Controller_Request_Http && !$request instanceof Lib_Controller_Request) {
      return;
    }

    $contentType = $request->getHeader('Content-Type');
    if ($contentType == self::APPLICATION_JSON && ($request->isPut() || $request->isPost())) {
      $jsonParams = Zend_Json::decode($request->getRawBody());
      $request->setParams($jsonParams);
    };
  }
}
