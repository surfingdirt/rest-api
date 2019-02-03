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
    if (!isset($data['mediaType']) ||
      !in_array($data['mediaType'], array(
        Media_Item::TYPE_PHOTO, Media_Item::TYPE_VIDEO
      ))) {
      throw new Api_Exception_BadRequest(
        "Bad media type: '${data['mediaType']}'",
        Api_ErrorCodes::MEDIA_BAD_MEDIA_TYPE);
    }

    $this->_table = $data['mediaType'] == Media_Item::TYPE_PHOTO ? new Api_Media_Photo() : new Api_Media_Video();

    $object = $this->_table->createRow();

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
      throw new Api_Exception('Saving failed but errors were not detected early');
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