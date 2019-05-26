<?php

class IndexController extends Zend_Rest_Controller
{
  public function init()
  {
    Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
  }

  public function indexAction()
  {
  }

  public function getAction()
  {
  }

  public function postAction()
  {
  }

  public function putAction()
  {
  }

  public function deleteAction()
  {
  }

  public function optionsAction()
  {
    $this->getResponse()->setHeader('Access-Control-Allow-Headers', 'withCredentials');
    $this->getResponse()->setHeader('Access-Control-Allow-Methods', 'OPTIONS, INDEX, GET, POST, PUT, DELETE');
    $this->getResponse()->setHeader('Access-Control-Allow-Credentials', 'true');
  }

}