<?php
class IndexController extends Zend_Rest_Controller
{
	public function indexAction()
	{
		$toto = 52;
		$this->view->toto = $toto;
	}

	public function getAction() {}

  /**
   * The post action handles POST requests; it should accept and digest a
   * POSTed resource representation and persist the resource state.
   */
  public function postAction() {}

  /**
   * The put action handles PUT requests and receives an 'id' parameter; it
   * should update the server resource state of the resource identified by
   * the 'id' value.
   */
  public function putAction() {}

  /**
   * The delete action handles DELETE requests and receives an 'id'
   * parameter; it should update the server resource state of the resource
   * identified by the 'id' value.
   */
  public function deleteAction() {}


  public function optionsAction()
  {
    $this->getResponse()->setHeader('Access-Control-Allow-Headers', 'withCredentials');
    $this->getResponse()->setHeader('Access-Control-Allow-Methods', 'OPTIONS, INDEX, GET, POST, PUT, DELETE');
    $this->getResponse()->setHeader('Access-Control-Allow-Credentials', 'true');
  }

}