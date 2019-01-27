<?php

class UsersAlbumsController extends Api_Controller_Action
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

    $this->view->resources = $resources;
  }

  protected function _getWhereClause(User_Row $user)
  {
    $userId = $this->_request->getParam('id');
    if (in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN)) || $user->getId() == $userId) {
      $return = '1';
    } else {
      $return = $this->_table->getAdapter()->quoteInto('status = ?', Data::VALID);
    }

    $return .= $this->_table->getAdapter()->quoteInto(' AND submitter = ?', $userId);

    $mainAlbums = implode(', ', Media_Album::$mainAlbumIds);
    $return .= " AND id NOT IN($mainAlbums)";

    return $return;
  }
}
