<?php

class UserController extends Api_Controller_Action
{
  const ACTIVATION_KEY_PARAMNAME = 'aK';

  public $listKey = 'date';

  protected function _getWhereClause(User_Row $user)
  {
    $statuses = array(
      User::STATUS_MEMBER, User::STATUS_WRITER, User::STATUS_EDITOR, User::STATUS_ADMIN,
    );
    if (in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))) {
      $statuses[] = User::STATUS_BANNED;
      $statuses[] = User::STATUS_PENDING;
    }
    $where = 'status IN("' . implode($statuses, '", "') . '")';

    return $where;
  }

  /**
   * This method sends the registration confirmation email
   */
  protected function _postObjectCreation($object, $data)
  {
    try {
      if (APPLICATION_ENV === 'test') {
        return true;
      }

      // This seems to stop the problems when calling from UI:
//      return true;

      // send email for password and confirmation
      $params['activationKey'] = $object->activationKey;
      $params['userId'] = $object->getId();
      $params['username'] = $object->getTitle();
      $params['userP'] = $data[User::INPUT_PASSWORD];

      // Send Email
      $emailer = new Lib_Controller_Helper_Emailer();
      $emailStatus = $emailer->sendEmail($object->{User::COLUMN_EMAIL}, $params,
        Lib_Controller_Helper_Emailer::REGISTRATION_EMAIL);
    } catch (Exception $e) {
      $this->_cleanUpAfterCreationFailure($object, $data);
      $emailStatus = false;
      $msg = "Email error" . $e->getMessage();
      Globals::getLogger()->registrationError($msg);
    }

    return $emailStatus;
  }

  protected function _cleanUpAfterCreationFailure($object, $data)
  {
    $where = $this->_table->getAdapter()->quoteInto(User::COLUMN_USERID . ' = ?', $object->getId());
    $this->_table->delete($where);
  }

  public function meAction()
  {
    if ($this->_user->status == User::STATUS_GUEST) {
      $attributes = $this->_accessor->getReadAttributes($this->_user);
      $user = new stdClass();
      foreach ($attributes as $key) {
        $user->$key = null;
      }
      $user->status = User::STATUS_GUEST;
      $this->view->output = $user;
      return;
    }

    $this->view->output = $this->_accessor->getObjectData($this->_user);
  }

  public function emailExistsAction()
  {
    if (!isset($_GET['email'])) {
      $output = false;
    } else {
      $table = new User();
      $user = $table->findByEmail($_GET['email']);
      $output = User_Row && $user->isValidUser();
    }

    $this->view->output = $output;
  }

  public function usernameExistsAction()
  {
    if (!isset($_GET['username'])) {
      $output = false;
    } else {
      $table = new User();
      $user = $table->findByName($_GET['username']);
      $output = $user->isValidUser();
    }

    $this->view->output = $output;
  }
}