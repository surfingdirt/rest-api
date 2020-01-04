<?php

class CustomController extends Zend_Controller_Action
{
  const ACTIVATION_KEY_PARAMNAME = 'aK';

  public function init()
  {
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setViewScriptPathSpec('view.phtml');
    $this->getResponse()->setRawHeader('Content-Type: application/json');

  }

  /**
   * This action updates a user's status to member
   * from pending. The only way a user can trigger this
   * is by clicking a link in the signup enail they received.
   * Deprecated because users are members by default.
   *
   * @deprecated
   * @throws Api_Exception_BadRequest
   * @throws Api_Exception_NotFound
   * @throws Api_Exception
   */
  public function userConfirmationAction()
  {
    $id = $this->_request->getParam('id');
    $data = $this->_getPut();
    if (!isset($data[self::ACTIVATION_KEY_PARAMNAME])) {
      throw new Api_Exception_BadRequest('Missing activation key');
    }
    $activationKey = $data[self::ACTIVATION_KEY_PARAMNAME];

    if (!$user = $this->_getUserFromIdAndKey($id, $activationKey)) {
      throw new Api_Exception_NotFound();
    }

    if ($user->{User::COLUMN_STATUS} == User::STATUS_PENDING) {
      $user->{User::COLUMN_STATUS} = User::STATUS_MEMBER;
      $user->date = Utils::date('Y-m-d H:i:s');
      $userId = $user->save();
      if ($id !== $userId) {
        Globals::getLogger()->registrationError("Account activation: user save failed - userId=$id, key=$activationKey", Zend_Log::INFO);
        throw new Api_Exception('Activation failed');
      }

      $this->view->output = array(
        'status' => true,
        'alreadyDone' => false
      );
      $this->_savePendingUserIdentity($userId);
    } else {
      $this->view->output = array(
        'status' => true,
        'alreadyDone' => true,
      );
    }
  }

  /**
   * Sends an email to a user
   *
   * /lost-password/
   */
  public function lostPasswordAction()
  {
    // Update user with a new password and a new activation key
    $userTable = new Api_User();
    $where = $userTable->getAdapter()->quoteInto(User::COLUMN_USERNAME . ' = ?',
      $this->_request->getParam(User::INPUT_USERNAME));
    $user = $userTable->fetchRow($where);

    if (empty($user)) {
      throw new Api_Exception_NotFound();
    }

    $newPassword = $this->_generateNewPassword();
    $authManager = new Lib_Auth_Manager(Globals::getMainDatabase());
    $saltedPassword = $authManager->makeSaltedHash($newPassword, $user->salt);
    $user->newPassword = $saltedPassword;
    $user->activationKey = $this->_generateActivationKey();
    $id = $user->save();

    if ($id != $user->{User::COLUMN_USERID}) {
      $this->view->output = array('errors' => $errors, 'code' => Api_ErrorCodes::USER_NEW_PASSWORD_FAILED);
      return;
    }

    $destinationParts = [
      APP_URL,
      '/rider/',
      $user->{User::COLUMN_USERID},
      '/activate-new-password',
      '?',
      self::ACTIVATION_KEY_PARAMNAME,
      '=',
      $user->activationKey
    ];

    $destination = implode('', $destinationParts);

    $params = array(
      'username' => $user->{User::COLUMN_USERNAME},
      'destination' => $destination,
      'newPassword' => $newPassword,
    );

    if (APPLICATION_ENV == 'test') {
      $this->view->output = [
        'userId' => $user->{User::COLUMN_USERID},
        'username' => $user->{User::COLUMN_USERNAME},
        'email' => $user->{User::COLUMN_EMAIL},
        'activationKey' => $user->activationKey,
        'newPassword' => $newPassword,
      ];
      return;
    }

    $emailer = new Lib_Controller_Helper_Emailer();
    $this->view->output = $emailer->sendEmail($user->{User::COLUMN_EMAIL}, $params,
      Lib_Controller_Helper_Emailer::LOST_PASSWORD_EMAIL);
  }

  public function activateNewPasswordAction()
  {
    $newKey = $this->getRequest()->getParam(self::ACTIVATION_KEY_PARAMNAME);
    $userId = $this->getRequest()->getParam('id');

    $user = $this->_getUserFromIdAndKey($userId, $newKey);
    if (!$user) {
      Globals::getLogger()->info("New password activation: user retrieval failed - userId=$userId, key=$newKey", Zend_Log::INFO);
      throw new Api_Exception_NotFound();
    }

    $user->{User::COLUMN_PASSWORD} = $user->newPassword;
    $user->newPassword = '';
    $user->activationKey = '';

    $id = $user->save();
    if ($id != $user->{User::COLUMN_USERID}) {
      throw new Api_Exception('Password activation failed');
    }

    $this->_savePendingUserIdentity($userId);

    $this->view->output = array('status' => true);
  }

  /**
   * Return the user with given userId and activation key
   *
   * @param string $userId
   * @param string $activationKey
   * @return User_Row
   */
  private function _getUserFromIdAndKey($userId, $activationKey)
  {
    $table = new Api_User();
    $where = $table->getAdapter()->quoteInto(User::COLUMN_USERID . ' = ?', $userId);
    $where2 = $table->getAdapter()->quoteInto(' AND activationKey = ?', $activationKey);
    $user = $table->fetchRow($where . $where2);

    return $user;
  }

  /**
   * Save user identity while waiting for confirmation
   *
   * @param integer $userId
   */
  private function _savePendingUserIdentity($userId)
  {
    $userData = new stdClass();
    $userData->{User::COLUMN_USERID} = $userId;
    $userData->sessionId = session_id();
    $userData->lastLogin = Utils::date('Y-m-d H:i:s');
    Zend_Auth::getInstance()->getStorage()->write($userData);
  }

  /**
   * Generate a password
   *
   * @return string
   */
  private function _generateNewPassword()
  {
    $password = Utils::getRandomKey(8);
    return $password;
  }

  /**
   * Generate an activation key
   *
   * @return string
   */
  private function _generateActivationKey()
  {
    $key = Utils::getRandomKey(32);
    return $key;
  }

  protected function _getPut()
  {
    $data = $this->_request->getParams();
    unset($data['module']);
    unset($data['controller']);
    unset($data['action']);
    unset($data['id']);

    //error_log('PUT '.var_export($data, TRUE));

    return $data;
  }
}