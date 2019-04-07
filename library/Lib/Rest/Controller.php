<?php

abstract class Lib_Rest_Controller extends Zend_Rest_Controller
{
  public function init()
  {
    parent::init();

    // This controller only has a JSON output
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setViewScriptPathSpec('view.phtml');
    $this->getResponse()->setRawHeader('Content-Type: application/json; charset=UTF-8');
  }

  public function optionsAction()
  {
    $this->getResponse()->setHeader('Access-Control-Allow-Methods', 'OPTIONS, POST, DELETE');
  }

  protected function _unauthorised($errorId = null, $code = null)
  {
    throw new Api_Exception_Unauthorised($errorId, $code);
  }
}