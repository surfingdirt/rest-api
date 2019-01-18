<?php

class MediaController extends Api_Controller_Action
{
  public function listAction()
  {
    throw new Api_Exception_Unauthorised();
  }

  public function postAction()
  {
    $data = $this->_request->getPost();
    if (!isset($data['mediaType']) || $data['mediaType'] != Media_Item::TYPE_PHOTO) {
      $data['mediaType'] = Media_Item::TYPE_PHOTO;
    }

    $this->_table = $data['mediaType'] == Media_Item::TYPE_PHOTO ? new Api_Media_Photo() : new Api_Media_Video();

    $object = $this->_table->createRow();
    // TODO: Move this to Api_Media_Photo and new Api_Media_Video
    $object->mediaType = $data['mediaType'];

    if (!$object->isCreatableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $this->_preObjectCreation($object, $data);
    list($id, $errors) = $this->_accessor->createObjectWithData($object, $data);

    if ($errors) {
      $this->getResponse()->setRawHeader('HTTP/1.1 400 Bad Request');
      $this->view->output = array('errors' => $errors);
    } else if ($id) {
      $this->_postObjectCreation($object, $data);
      $this->view->output = $this->_accessor->getObjectData(
        $object,
        'get',
        null
      );
    } else {
      throw new Api_Exception('Saving failed');
    }
  }

  public function putAction()
  {
    $id = $this->_request->getParam('id');
    $data = $this->_getPut();

    $result = $this->_table->find($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }

    $object = Media_Item_Factory::buildItem($object->id, $object->mediaType);
    if (!$object->isEditableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $this->_preObjectUpdate($object, $data);
    $errors = $this->_accessor->updateObjectWithData($object, $data);
    if (empty($errors)) {
      $this->_postObjectUpdate($object, $data);
    } else {
      $this->getResponse()->setRawHeader('HTTP/1.1 400 Bad Request');
    }
    $this->view->errors = $errors;
    $this->view->resourceId = $id;

    // TODO: retourner l'objet entier, pas juste l'id
  }
}