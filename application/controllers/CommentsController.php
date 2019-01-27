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

    $this->view->resources = $resources;
  }

  protected function _preObjectCreation($object, $data)
  {
    try {
      $object->parentItem = Data::factory(
        $this->_request->getParam('itemId'),
        $this->_request->getParam('itemType')
      );
    } catch (Lib_Exception_NotFound $e) {
      throw new Api_Exception_NotFound();
    }
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

  public function deleteAction()
  {
    // If itemType or itemId are passed, that's a mistake
    if ($this->_request->getParam('itemType') || $this->_request->getParam('itemId')) {
      throw new Api_Exception_BadRequest();
    }

    parent::deleteAction();
  }

}