<?php

class AlbumController extends Api_Controller_Action
{
  public $listCount = 5;

  public $listDir = 'desc';

  protected function _preObjectCreation($object, $data)
  {
    $object->id = Utils::uuidV4();
    $object->albumType = Media_Album::TYPE_SIMPLE;
    $object->albumAccess = Media_Album::ACCESS_PUBLIC;
    $object->albumCreation = Media_Album::CREATION_USER;
    $object->setNotification(true);
  }

  public function deleteAction()
  {
    $id = $this->_request->getParam('id');
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $result = $this->_table->find($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }

    if ($object->albumType != Media_Album::TYPE_SIMPLE) {
      throw new Api_Exception_BadRequest('Album not deletable', Api_ErrorCodes::NON_STATIC_ALBUM_CANNOT_BE_DELETED);
    }

    $itemSet = $object->getItemSet();
    if (!$itemSet || count($itemSet) > 0) {
      throw new Api_Exception_BadRequest('Album not empty', Api_ErrorCodes::STATIC_ALBUM_NOT_EMPTY);
    }

    if (!$object->isDeletableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $this->_preObjectDelete($object);
    if ($status = $this->_accessor->deleteObject($object)) {
      $this->_postObjectDelete($object);
    }
    $this->view->output = array('status' => $status);
  }

  protected function _getWhereClause(User_Row $user)
  {
    if (in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))) {
      $return = '1';
    } else {
      $return = $this->_table->getAdapter()->quoteInto('status = ?', Data::VALID);
    }

    $return .= $this->_table->getAdapter()->quoteInto(' AND albumType = ?', Media_Album::TYPE_SIMPLE);
    $return .= $this->_table->getAdapter()->quoteInto(' AND albumAccess = ?', Media_Album::ACCESS_PUBLIC);

    return $return;
  }
}