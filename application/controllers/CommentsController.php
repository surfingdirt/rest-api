<?php

class CommentsController extends Api_Controller_Action
{
  public function listAction()
  {
    if (!($itemType = $this->_request->getParam('itemType'))
      || !($itemId = $this->_request->getParam('itemId'))) {
      throw new Api_Exception_BadRequest();
    }

    try {
      $item = Data::factory($itemId, $itemType, true);
    } catch (Lib_Exception_NotFound $e) {
      throw new Api_Exception_NotFound();
    }

    $resources = array();
    foreach ($item->getComments($this->_user, $this->_acl) as $object) {
      $resources[] = $this->_accessor->getObjectData($object, $this->_request->getActionName());
    }

    $this->view->output = $resources;
  }

  protected function _preObjectCreation($object, $data)
  {
    $object->id = Utils::uuidV4();
    try {
      $object->parentItem = Data::factory(
        $data['itemId'],
        $data['itemType']
      );
    } catch (Lib_Exception_NotFound $e) {
      throw new Api_Exception_NotFound();
    }
  }

  protected function _postObjectCreation($object, $data)
  {
    $parent = $object->parentItem;
    $parent->clearCommentsCache();
  }

  public function getAction()
  {
    // If itemType or itemId are passed, that's a mistake
    if ($this->_request->getParam('itemType') || $this->_request->getParam('itemId')) {
      throw new Api_Exception_BadRequest();
    }

    parent::getAction();
  }

  public function putAction()
  {
    // If itemType or itemId are passed, that's a mistake
    if ($this->_request->getParam('itemType') || $this->_request->getParam('itemId')) {
      throw new Api_Exception_BadRequest();
    }

    parent::putAction();
  }

  protected function _postObjectUpdate($object, $data)
  {
    $parent = $object->getParentItemfromDatabase();
    $parent->clearCommentsCache();
  }

  public function deleteAction()
  {
    // If itemType or itemId are passed, that's a mistake
    if ($this->_request->getParam('itemType') || $this->_request->getParam('itemId')) {
      throw new Api_Exception_BadRequest();
    }
  }

  protected function _postObjectDelete($object)
  {
    $parent = $object->getParentItemfromDatabase();
    $parent->clearCommentsCache();
  }
}