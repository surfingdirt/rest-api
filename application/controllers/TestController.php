<?php
class TestController extends Zend_Controller_Action
{
	public function init()
	{
    if(APPLICATION_ENV != 'test'){
      throw new Api_Exception_Unauthorised();
    }

    Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->setViewScriptPathSpec('view.phtml');
		$this->getResponse()->setRawHeader('Content-Type: application/json');
	}

	public function freezeTimeAction()
	{
		$datetime = $this->_request->getParam('datetime');
		if ($datetime === 'NOW') {
      $datetime = date("Y-m-d H:i:s");
    }

		if(!preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $datetime, $matches)){
      $this->view->output = array(
        status => false,
        datetime => null,
        date => null,
        timestamp => null,
      );
      return;
		}

		$parts = explode(" ", $datetime);
		$date = $parts[0];

		$cache = Globals::getGlobalCache();
		$cache->save($datetime, 'datetime');
		$cache->save($date, 'date');
		$cache->save(strtotime($datetime), 'timestamp');

    Globals::getLogger()->test("Setting time to $datetime");

		$output = array(
      'status' => true,
      'datetime' => $cache->load('datetime'),
      'date' => $cache->load('date'),
      'timestamp' => $cache->load('timestamp'),
    );
    $this->view->output = $output;
	}

	public function getTimeAction()
	{
		$this->view->status = true;
		$this->view->datetime = Utils::date("Y-m-d H:i:s");
		$this->view->date = Utils::date("Y-m-d");
	}

  public function clearCacheAction()
  {
    try {
      Globals::getGlobalCache()->clean();
      $this->view->output = array('status' => 'ok');
    } catch (Exception $e) {
      $this->view->output = array('error' => $e->getMessage());
    }
  }
}