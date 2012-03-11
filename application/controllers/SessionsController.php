<?php
class SessionsController extends Zend_Rest_Controller
{
	const MISSING_VALUE = 'missingValue';
	const FAILED_TO_LOGIN = 'failedToLogin';
	const LOGIN_SYSTEM_ERROR = 'loginSystemError';
	const LOGOUT_SYSTEM_ERROR = 'logoutSystemError';
	
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
        	$this->_forbidden(self::MISSING_VALUE);
        	return;
        }
        $authAdapter = new Lib_Auth_Adapter($db, $username, $password);
        $result = $authAdapter->authenticate();
        if (!$result->isValid()) {
        	$this->_forbidden(self::FAILED_TO_LOGIN);
        	return;
        }

        $userRow = $authAdapter->getResultRowObject(array(User::COLUMN_USERID));
        $userTable = new Api_User();
        $results = $userTable->find($userRow->{User::COLUMN_USERID});
        if(!$results){
        	$this->_forbidden(self::LOGIN_SYSTEM_ERROR);
        	return;
        }
        $user = $results->current();
        if(!$user){
        	$this->_forbidden(self::LOGIN_SYSTEM_ERROR);
        	return;
        }

        $this->_clearSession();

        $lastLogin = $user->{User::COLUMN_LAST_LOGIN};

        $user->{User::COLUMN_LAST_LOGIN} = Utils::date("Y-m-d H:i:s");
        $user->save();

        session_regenerate_id();
		$_SESSION[User::COLUMN_USERID] = $this->view->resourceId = $userRow->{User::COLUMN_USERID};
		
		Globals::setUser($user);
		$accessor = new Api_User_Accessor($user, Globals::getAcl());
		$this->view->rider = $accessor->getObjectData($user);
		
		$this->view->sessionId = session_id();
		$this->view->lastLogin = $lastLogin;
    }

    public function deleteAction()
    {
		/**
		 * We need to destroy the session with a given id
		 * However, session ids are passed in a cookie,
		 * or in a GET parameter, so
		 * we don't need this id parameter. Let's just
		 * make sure parameter and param match.
		 */
    	
    	//$this->_forbidden(self::LOGOUT_SYSTEM_ERROR);return;
    	if($this->_request->getParam('id') != session_id()){
    		$this->_forbidden(self::LOGOUT_SYSTEM_ERROR);

    		$this->view->sessionId = session_id();
    		return;
    	}

		$this->_clearSession();
    	$this->view->sessionId = session_id();
    	
    	$table = new Api_User();
    	$results = $table->find(0);
    	$guest = $results->current();
    	
    	$rider = new stdClass();
    	$rider->userId = $guest->getId();
    	$rider->username = $guest->getTitle();
    	
    	$this->view->rider = $rider;
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
    	$this->getResponse()->setRawHeader('HTTP/1.1 403 Forbidden');
    	$this->view->resourceId = 0;
    	$this->view->errorId = $errorId;
    }

	public function optionsAction()
	{
		$this->getResponse()->setHeader('Access-Control-Allow-Methods', 'OPTIONS, INDEX, GET, POST, PUT, DELETE');
	}
}