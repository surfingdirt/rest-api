<?php
class MediaController extends Api_Controller_Action
{
  public $listDir = 'DESC';

  protected function _getWhereClause(User_Row $user)
  {
    $db = $this->_table->getAdapter();

    $ids = $this->_request->getParam('ids');
    if (!$ids) {
      throw new Api_Exception_BadRequest();
    }
    $ids = explode(',', $ids);
    $quoted = [];
    foreach ($ids as $id) {
      $quoted[] = $db->quote($id);
    }
    $glued = implode(",", $quoted);

    if (in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))) {
      $where = '1';
    } else {
      $userId = $user->getId();
      $where = $db->quoteInto("(status = ? OR submitter = '$userId')", Data::VALID);
    }

    $where .= " AND id IN ($glued)";

    return $where;
  }

  public function postAction()
  {
    $data = $this->_getBodyParams();
    $object = Api_Media_Factory::createItem($data['mediaType']);

    $this->_preObjectCreation($object, $data);
    list($id, $errors) = $this->_accessor->createObjectWithData($object, $data);

    if ($errors) {
      $this->_badRequest();
      $this->view->output = array('errors' => $errors, 'code' => Api_ErrorCodes::FORM_BAD_INPUT);
    } else if ($id) {
      $this->_postObjectCreation($object, $data);
      $this->view->output = $this->_accessor->getObjectData(
        $object,
        'get',
        null
      );
    } else {
      // This should happen only when something was not handled properly.
      throw new Api_Exception('Saving failed but errors were not detected early');
    }
  }

  public function putAction()
  {
    $id = $this->_request->getParam('id');
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $data = $this->_getBodyParams();
    $object = Api_Media_Factory::buildItemById($id);

    $errors = $this->_preObjectUpdate($object, $data);
    if (!$errors) {
      $errors = $this->_accessor->updateObjectWithData($object, $data);
    }
    if ($errors) {
      $this->_badRequest();
      $this->view->output = array('errors' => $errors, 'code' => Api_ErrorCodes::FORM_BAD_INPUT);
    } else {
      $this->_postObjectUpdate($object, $data);
      $this->view->output = $this->_accessor->getObjectData(
        $object,
        'get',
        null
      );
    }
  }

  public function deleteAction()
  {
    $id = $this->_request->getParam('id');
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $object = Api_Media_Factory::buildItemById($id);

    if (!$object->isDeletableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $this->_preObjectDelete($object);
    if ($status = $this->_accessor->deleteObject($object)) {
      $this->_postObjectDelete($object);
    }
    $this->view->output = array('status' => $status);
  }
}