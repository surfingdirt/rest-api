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
    if (APPLICATION_ENV != 'production') {
      return true;
    }
    // send email for password and confirmation
    $params['activationKey'] = $object->activationKey;
    $params['link'] = APP_URL . Globals::getRouter()->assemble(array(), 'userconfirmation');
    $params['link'] .= '?' . User::COLUMN_USERID . '=' . $object->getId() . '&' . self::ACTIVATION_KEY_PARAMNAME . "={$object->activationKey}";
    $params['site'] = APP_NAME;

    try {
      // Send Email
      $emailer = new Lib_Controller_Helper_Emailer();
      $emailStatus = $emailer->sendEmail($object->{User::COLUMN_EMAIL}, $params);
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
}