<?php

class CommentsController extends Api_Controller_Action
{
  public function listAction()
  {
    if (($itemType = $this->_request->getParam('itemType'))
      && ($itemId = $this->_request->getParam('itemId'))) {
      // List comments for a given parent object:
      try {
        $item = Data::factory($itemId, $itemType, true);
      } catch (Lib_Exception_NotFound $e) {
        throw new Api_Exception_NotFound();
      }

      $resources = array();
      foreach ($item->getComments($this->_user, $this->_acl) as $object) {
        $resources[] = $this->_accessor->getObjectData($object, $this->_request->getActionName());
      }
    } else if ($this->_request->getParam('ids')) {
      // List comments based on a number of ids
      $count = $this->getRequest()->getParam('count', $this->listCount);
      $start = $this->getRequest()->getParam('start', $this->listStart);

      $dir = $this->_getDir();
      $sort = $this->_getSort();
      $where = $this->_getWhereClause($this->_user);
      $where .= $this->_getWhereClauseForBatchGet($this->_user);

      $results = $this->_getAllObjects($where, $sort, $dir, $count, $start);

      $resources = array();
      foreach ($results as $object) {
        $resources[] = $this->_accessor->getObjectData($object, $this->_request->getActionName(), $this->_request->getParams());
      }
    } else {
      throw new Api_Exception_BadRequest();
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

    $id = $this->_request->getParam('id');
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $result = $this->_table->find($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }
    if (!$object->isDeletableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $parent = $object->getParentItemfromDatabase();
    if ($status = $this->_accessor->deleteObject($object)) {
      $parent->clearCommentsCache();
    }
    $this->view->output = array('status' => $status);
  }

  protected function _postObjectDelete($object)
  {
    $parent = $object->getParentItemfromDatabase();
    $parent->clearCommentsCache();
  }
}