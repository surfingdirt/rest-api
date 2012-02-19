<?php
class SessionsController extends Zend_Rest_Controller
{
	public function init()
	{
		parent::init();

		// This controller only has a JSON output
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setViewScriptPathSpec('sessions/:action.json');
	}

    /**
     * Processes a login request (session creation)
     */
    public function postAction()
    {
    	$db = Globals::getMainDatabase();
    	$username = $this->getRequest()->getPost(User::INPUT_USERNAME);
    	$password = $this->getRequest()->getPost(User::INPUT_PASSWORD);

    	if (empty($username) || empty($password)) {
        	$this->_forbidden(0);
        	return;
        }
        $authAdapter = new Lib_Auth_Adapter($db, $username, $password);
        $result = $authAdapter->authenticate();
        if (!$result->isValid()) {
        	$this->_forbidden(1);
        	return;
        }

        $userRow = $authAdapter->getResultRowObject(array(User::COLUMN_USERID));
        $userTable = new Api_User();
        $results = $userTable->find($userRow->{User::COLUMN_USERID});
        if(!$results){
        	$this->_forbidden(2);
        	return;
        }
        $user = $results->current();
        if(!$user){
        	$this->_forbidden(3);
        	return;
        }

        $this->_clearSession();

        $lastLogin = $user->{User::COLUMN_LAST_LOGIN};

        $user->{User::COLUMN_LAST_LOGIN} = Utils::date("Y-m-d H:i:s");
        $user->save();

        session_regenerate_id();
		$_SESSION[User::COLUMN_USERID] = $this->view->resourceId = $userRow->{User::COLUMN_USERID};
		$this->view->sessionId = session_id();
		$this->view->lastLogin = $lastLogin;
    }

    public function deleteAction()
    {
		/**
		 * We need to destroy the session with a given id
		 * However, session ids are passed in a cookie, so
		 * we don't need this id parameter. Let's just
		 * make sure parameter and cookie match.
		 */

    	if($this->_request->getParam('id') != session_id()){
    		$this->_forbidden(0);

    		$this->view->sessionId = session_id();
    		return;
    	}

		$this->_clearSession();
    	$this->view->sessionId = session_id();
    }

    protected function _clearSession()
    {
    	$_SESSION = array(User::COLUMN_USERID => 0);
    	$this->view->resourceId = 0;
    	session_regenerate_id();
    }

    /**
     *FORBIDDEN HTTP METHODS
     */
    public function listAction()
    {
    	$this->_forbidden(0);
    }

    public function indexAction()
    {
    	$this->_forbidden(0);
    }

    public function getAction()
    {
    	$this->_forbidden(0);
    }

    public function putAction()
    {
       	$this->_forbidden(0);
    }

    protected function _forbidden($errorId)
    {
    	//die($errorId);
    	$this->getResponse()->setRawHeader('HTTP/1.1 403 Forbidden');
    	$this->view->resourceId = 0;
    	$this->view->errorId = $errorId;
    }
}