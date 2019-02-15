<?php
class MediaController extends Api_Controller_Action
{
  public function listAction()
  {
    throw new Api_Exception_Unauthorised();
  }

  public function postAction()
  {
    $data = $this->_getBodyParams();
    $object = Api_Media_Factory::createItem($data['mediaType']);

    $this->_preObjectCreation($object, $data);
    list($id, $errors) = $this->_accessor->createObjectWithData($object, $data);

    if ($errors) {
      $this->_badRequest();
      $this->view->output = array('errors' => $errors);
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
      $this->view->output = array('errors' => $errors);
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