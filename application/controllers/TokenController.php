<?php

class TokenController extends Lib_Rest_Controller
{
  const MISSING_VALUE = 'missingValue';
  const FAILED_TO_LOGIN = 'failedToLogin';
  const LOGIN_SYSTEM_ERROR = 'loginSystemError';
  const EXISTING_TOKEN = 'existingToken';
  const INVALID_OAUTH_TOKEN = 'invalidOAuthToken';

  /**
   * Processes a login request (token creation)
   */
  public function postAction()
  {
    $token = Globals::getJWT();
    if ($token) {
      return $this->_unauthorised(self::EXISTING_TOKEN, Api_ErrorCodes::TOKEN_EXISTING);
    }

    $db = Globals::getMainDatabase();
    $username = $this->getRequest()->getParam(User::INPUT_USERNAME);
    $password = $this->getRequest()->getParam(User::INPUT_PASSWORD);

    if (empty($username) || empty($password)) {
      return $this->_unauthorised(self::MISSING_VALUE, Api_ErrorCodes::TOKEN_MISSING_VALUE);
    }
    $authManager = new Lib_Auth_Manager($db);
    $userId = $authManager->verifyLogin($username, $password);
    if (!$userId) {
      return $this->_unauthorised(self::FAILED_TO_LOGIN, Api_ErrorCodes::TOKEN_FAILED_TO_LOGIN);
    }

    $userTable = new Api_User();
    $results = $userTable->find($userId);
    if (!$results) {
      return $this->_unauthorised(self::LOGIN_SYSTEM_ERROR, Api_ErrorCodes::TOKEN_LOGIN_SYSTEM_ERROR);
    }
    $user = $results->current();
    if (!$user) {
      return $this->_unauthorised(self::LOGIN_SYSTEM_ERROR, Api_ErrorCodes::TOKEN_LOGIN_SYSTEM_ERROR);
    }

    $token = $this->_doLogin($user);

    $this->view->output = array(
      'token' => $token,
    );
  }

  protected function _doLogin($user)
  {
    $user->{User::COLUMN_LAST_LOGIN} = Utils::date("Y-m-d H:i:s");
    $user->save();
    Globals::setUser($user);

    // JWT
    $token = Lib_JWT::create(JWT_SECRET, Utils::date("timestamp"), $user->getId());
    Globals::setJWT($token);

    return $token;
  }

  public function oauthLoginAction()
  {
    $token = Globals::getJWT();
    if ($token) {
      return $this->_unauthorised(self::EXISTING_TOKEN, Api_ErrorCodes::TOKEN_EXISTING);
    }

    $data = $this->_getBodyParams();
    $fbToken = Lib_Firebase::getVerifiedToken(FIREBASE_PROJECT_ID, $data['token']);
    if (!$fbToken) {
      // Token is invalid
      return $this->_unauthorised(self::INVALID_OAUTH_TOKEN, Api_ErrorCodes::INVALID_OAUTH_TOKEN);
    }

    $userData = Lib_Firebase::getUserDataFromToken($fbToken);

    $emailColumn = User::COLUMN_EMAIL;
    $userTable = new Api_User();
    $where = $userTable->getAdapter()->quoteInto("$emailColumn = ?", $userData['email']);
    $user = $userTable->fetchRow($where);
    if (!$user) {
      return $this->_unauthorised(self::FAILED_TO_LOGIN, Api_ErrorCodes::TOKEN_FAILED_TO_LOGIN);
    }

    $token = $this->_doLogin($user);

    $this->view->output = array(
      'token' => $token,
    );
  }

  /**
   * Returns all user-land parameters.
   * @return array
   * @throws Zend_Controller_Action_Exception
   */
  protected function _getBodyParams()
  {
    $params = $this->_request->getParams();

    unset($params[$this->_request->getControllerKey()]);
    unset($params[$this->_request->getActionKey()]);
    unset($params[$this->_request->getModuleKey()]);
    unset($params['id']);

    return $params;
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
    $this->_unauthorised();
  }

  public function indexAction()
  {
    $this->_unauthorised();
  }

  public function getAction()
  {
    $this->_unauthorised();
  }

  public function putAction()
  {
    $this->_unauthorised();
  }
}