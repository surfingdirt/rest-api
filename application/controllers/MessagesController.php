<?php

class MessagesController extends Api_Controller_Action
{
  /**
   * The index action handles index/list requests; it should respond with a
   * list of the requested resources.
   */
  public function listAction()
  {
    if (!$this->_user->isLoggedIn()) {
      throw new Api_Exception_Unauthorised();
    }

    parent::listAction();
  }

  protected function _getWhereClause(User_Row $user)
  {

    if (in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))) {
      $where = 'toUser = ' . $user->getId();
    } else {
      $where = $this->_table->getAdapter()->quoteInto('status = ?', Data::VALID);
      $where .= ' AND toUser = ' . $user->getId();
    }

    if ($this->getRequest()->getParam('new')) {
      $where .= ' AND `read` = 0';
    }

    return $where;
  }

  public function deleteAction()
  {
    throw new Api_Exception_Unauthorised();
  }
}