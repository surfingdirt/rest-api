<?php

class TricksController extends Api_Controller_Action
{
  protected function _getWhereClause(User_Row $user)
  {
    if (in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))) {
      $return = '1';
    } else {
      $return = $this->_table->getAdapter()->quoteInto('status = ?', Data::VALID);
      $return .= $this->_table->getAdapter()->quoteInto('OR submitter = ?', $user->getId());
    }

    return $return;
  }
}