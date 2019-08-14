<?php

class UseralbumController extends Api_Controller_Action
{
  public function getAction()
  {
    throw new Api_Exception_Unauthorised();
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

  public function listAction()
  {
    $id = $this->_request->getParam('id');

    $where = $this->_getWhereClause($this->_user);

    $results = $this->_getAllObjects($where);

    if (empty($results)) {
      throw new Api_Exception_NotFound();
    }

    $resources = array();
    foreach ($results as $object) {
      $resources[] = $this->_accessor->getObjectData($object, $this->_request->getActionName(), $this->_request->getParams());
    }

    $this->view->output = $resources;
  }

  protected function _getWhereClause(User_Row $user)
  {

    $userId = $this->_request->getParam('id');
    $isSubmitter = $user->getId() == $userId;

    $where = '1';
    $where .= $this->_table->getAdapter()->quoteInto(' AND submitter = ?', $userId);
    $where .= $this->_table->getAdapter()->quoteInto(' AND albumType = ?', Media_Album::TYPE_SIMPLE);

    if (!$isSubmitter && !in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN)))  {
      $where .= $this->_table->getAdapter()->quoteInto(' AND status = ?', Data::VALID);
    }

    if (!$isSubmitter) {
      $where .= $this->_table->getAdapter()->quoteInto(' AND albumAccess = ?', Media_Album::ACCESS_PUBLIC);
    }

    $list = [];
    foreach (Media_Album::$mainAlbumIds as $id) {
      $list[] =  $this->_table->getAdapter()->quoteInto('?', $id);
    }

    $list = implode(',', $list);
    $where .= " AND id NOT IN ($list)";

    return $where;
  }
}
