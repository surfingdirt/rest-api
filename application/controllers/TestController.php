<?php
class TestController extends Zend_Controller_Action
{
	public function init()
	{
		Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->setViewScriptPathSpec('test/time.json');
		$this->getResponse()->setRawHeader('Content-Type: application/json');
	}

	public function freezeTimeAction()
	{
		if(APPLICATION_ENV != 'test'){
			throw new Api_Exception_Unauthorised();
		}

		$datetime = $this->_request->getParam('datetime');
		if ($datetime === 'NOW') {
      $datetime = date("Y-m-d H:i:s");
    }

		if(!preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-3]:[0-5][0-9]:[0-5][0-9]$/", $datetime, $matches)){
			$this->view->status = false;
			$this->view->datetime = null;
			$this->view->date = null;
		}

		$parts = explode(" ", $datetime);
		$date = $parts[0];

		$cache = Globals::getGlobalCache();
		$cache->save($datetime, 'datetime');
		$cache->save($date, 'date');
		$cache->save(strtotime($datetime), 'timestamp');
		
		$this->view->status = true;
		$this->view->datetime = $cache->load('datetime');
		$this->view->date = $cache->load('date');
		$this->view->timestamp = $cache->load('timestamp');
	}

	public function getTimeAction()
	{
		$this->view->status = true;
		$this->view->datetime = Utils::date("Y-m-d H:i:s");
		$this->view->date = Utils::date("Y-m-d");
	}
}