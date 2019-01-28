<?php

class ImageController extends Lib_Rest_Controller
{
  const FILE_KEY = 'files';

  public function init()
  {
    parent::init();

    try {
      $userId = Lib_JWT::setup($this->getRequest(), JWT_SECRET);
    } catch (Lib_JWT_Exception $e) {
      throw $e;
    }

    $userTable = new Api_User();
    $results = $userTable->find($userId);
    if ($results && $user = $results->current()) {
      Globals::setUser($user);
      $this->_user = $user;
    } else {
      throw new Api_Exception_BadRequest("Could not find user '$userId'");
    }

    $this->_table = new Api_Image();
  }

  public function postAction()
  {
    // Use a photo object as a proxy to determine ACLs
    $table = new Media_Item_Photo();
    $photo = $table->createRow();
    if (!$photo->isCreatableBy($this->_user, Globals::getAcl())) {
      return $this->_unauthorised();
    }

    $storageType = $this->getRequest()->getParam('type', null);
    if (!array_key_exists($storageType, Lib_Storage::$config)) {
      throw new Api_Exception_BadRequest('No known type given');
    }

    if (!isset($_FILES) || !isset($_FILES[self::FILE_KEY])) {
      throw new Api_Exception_BadRequest('No files found');
    }

    $output = array();
    foreach ($_FILES[self::FILE_KEY]['tmp_name'] as $i => $tmpFile) {
      $thisFileOutput = array();

      $id = Utils::uuidV4();
      try {
        if ($_FILES[self::FILE_KEY]['error'][$i]) {
          throw new Lib_Exception(
            "Uploaded file is marked with an error",
            Api_ErrorCodes::IMAGE_UPLOAD_FAILED);
        }

        Lib_Storage::storeFile($storageType, $tmpFile, $id);
        $imageRow = $this->_table->createRow(array(
          'id' => $id,
          'storageType' => $storageType,
        ));
        try {
          $imageRow->save();
        } catch (Zend_Db_Table_Exception $e) {
          throw new Lib_Exception(
            'Could not save image DB entry',
            Api_ErrorCodes::IMAGE_DB_SAVE_FAILURE);
        }
        $thisFileOutput['key'] = $id;
      } catch (Lib_Exception $e) {
        Lib_Storage::cleanUpFiles($storageType, $id);
        $thisFileOutput['error'] = $e->getCode();
      }

      $output[] = $thisFileOutput;
    }

    $this->view->output = $output;
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

    // Only owner and editor/admin have access
    if (!$object->isDeletableBy($this->_user, Globals::getAcl())) {
      throw new Api_Exception_Unauthorised();
    }

    try {
      Lib_Storage::cleanUpFiles($object->storageType, $id);
    } catch (Lib_Exception $e) {
      $this->view->output = array('error' => $e->getCode());
      return;
    }

    try {
      $where = $this->_table->getAdapter()->quoteInto('id = ?', $id);
      $this->_table->delete($where);
    } catch (Exception $e) {
      $this->view->output = array('error' => Api_ErrorCodes::IMAGE_DB_DELETE_FAILURE);
      return;
    }

    $this->view->output = array('status' => 'ok');
  }

  /*
   * All other methods are forbidden
   */

  public function indexAction()
  {
    $this->_unauthorised();
  }

  public function listAction()
  {
    $this->_unauthorised();
  }

  public function putAction()
  {
    $id = $this->_request->getParam('id');
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $result = $this->_table->find($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }

    $this->_unauthorised();
  }

  public function getAction()
  {
    $id = $this->_request->getParam('id');
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $result = $this->_table->find($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }

    $this->_unauthorised();
  }
}