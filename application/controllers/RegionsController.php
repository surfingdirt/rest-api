<?php

class RegionsController extends Api_Controller_Action
{
  public function listAction()
  {
    //TODO: list all regions, list regions per country
    // allow all regions to be requested if limit = 0 (default)
    parent::listAction();
  }

  public function postAction()
  {
    throw new Api_Exception_Unauthorised();
  }

  public function putAction()
  {
    throw new Api_Exception_Unauthorised();
  }

  public function deleteAction()
  {
    throw new Api_Exception_Unauthorised();
  }

  protected function _getWhereClause(User_Row $user)
  {
    $return = '1';
    $countryId = $this->_request->getParam('countryId');
    if (is_numeric($countryId)) {
      $return .= ' AND country = ' . $countryId;
    }

    if (!in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))) {
      $return .= ' AND ' . $this->_table->getAdapter()->quoteInto('status = ?', Data::VALID);
    }

    return $return;
  }
}