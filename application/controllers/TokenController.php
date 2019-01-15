<?php
use Lcobucci\JWT\ValidationData;
class TokenController extends Zend_Rest_Controller
{
  const MISSING_VALUE = 'missingValue';
  const FAILED_TO_LOGIN = 'failedToLogin';
  const LOGIN_SYSTEM_ERROR = 'loginSystemError';
  const LOGOUT_SYSTEM_ERROR = 'logoutSystemError';
  const EXISTING_TOKEN = 'existingToken';

  public function init()
  {
    parent::init();

    // This controller only has a JSON output
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setViewScriptPathSpec('view.phtml');
    $this->getResponse()->setRawHeader('Content-Type: application/json; charset=UTF-8');
  }

  /**
   * Processes a login request (token creation)
   */
  public function postAction()
  {
    $token = Globals::getJWT();
    if ($token) {
      return $this->_forbidden(self::EXISTING_TOKEN);
    }

    $db = Globals::getMainDatabase();
    $username = $this->getRequest()->getParam(User::INPUT_USERNAME);
    $password = $this->getRequest()->getParam(User::INPUT_PASSWORD);

    if (empty($username) || empty($password)) {
      return $this->_forbidden(self::MISSING_VALUE);
    }
    $authAdapter = new Lib_Auth_Adapter($db, $username, $password);
    $result = $authAdapter->authenticate();
    if (!$result->isValid()) {
      return $this->_forbidden(self::FAILED_TO_LOGIN);
    }

    $userRow = $authAdapter->getResultRowObject(array(User::COLUMN_USERID));
    $userTable = new Api_User();
    $results = $userTable->find($userRow->{User::COLUMN_USERID});
    if (!$results) {
      return $this->_forbidden(self::LOGIN_SYSTEM_ERROR);
    }
    $user = $results->current();
    if (!$user) {
      return $this->_forbidden(self::LOGIN_SYSTEM_ERROR);
    }
    $user->{User::COLUMN_LAST_LOGIN} = Utils::date("Y-m-d H:i:s");
    $user->save();
    Globals::setUser($user);

    // JWT
    $token = Lib_JWT::create(JWT_SECRET, Utils::date("timestamp"), $user->getId());
    Globals::setJWT($token);

    $this->view->output = array(
      'token' => $token,
    );
  }

  /**
   * Processes a logout request (token destruction)
   */
  public function deleteAction()
  {
    $matches = Lib_JWT::getHeaderMatches($this->getRequest());
    $token = $matches[1];

    // Add the token to the blacklist if it's still valid, otherwise clean up the list.
    if (Lib_JWT::isBlacklistable($token, JWT_SECRET)) {
      Lib_JWT_Blacklist::addToken($token);
    } else {
      Lib_JWT_Blacklist::cleanupInvalidAndExpiredTokens(JWT_SECRET);
    }
    Globals::clearJWT();
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
    $this->view->output = array('error' => $errorId);
  }

  public function optionsAction()
  {
    $this->getResponse()->setHeader('Access-Control-Allow-Methods', 'OPTIONS, POST, DELETE');
  }
}