<?php

class UseralbumController extends AlbumController
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
