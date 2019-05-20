<?php

class TokenController extends Lib_Rest_Controller
{
  const MISSING_VALUE = 'missingValue';
  const FAILED_TO_LOGIN = 'failedToLogin';
  const LOGIN_SYSTEM_ERROR = 'loginSystemError';
  const EXISTING_TOKEN = 'existingToken';

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
    $authAdapter = new Lib_Auth_Adapter($db, $username, $password);
    $result = $authAdapter->authenticate();
    if (!$result->isValid()) {
      return $this->_unauthorised(self::FAILED_TO_LOGIN, Api_ErrorCodes::TOKEN_FAILED_TO_LOGIN);
    }

    $userRow = $authAdapter->getResultRowObject(array(User::COLUMN_USERID));
    $userTable = new Api_User();
    $results = $userTable->find($userRow->{User::COLUMN_USERID});
    if (!$results) {
      return $this->_unauthorised(self::LOGIN_SYSTEM_ERROR, Api_ErrorCodes::TOKEN_LOGIN_SYSTEM_ERROR);
    }
    $user = $results->current();
    if (!$user) {
      return $this->_unauthorised(self::LOGIN_SYSTEM_ERROR, Api_ErrorCodes::TOKEN_LOGIN_SYSTEM_ERROR);
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
    $this->view->output = array(
      'logout' => 'success',
    );
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