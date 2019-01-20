<?php
class ImageController extends Lib_Rest_Controller
{
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

    if (!isset($_FILES) || !isset($_FILES['files'])) {
      throw new Api_Exception_BadRequest('No files found');
    }

    $output = array();
    foreach ($_FILES['files']['tmp_name'] as $i => $tmpFile) {
      $thisFileOutput = array();

      $uuid = Utils::uuidV4();
      try {
        if ($_FILES['files']['error'][$i]) {
          throw new Lib_Exception(
            "Uploaded file is marked with an error",
            Api_ErrorCodes::IMAGE_UPLOAD_FAILED);
        }

        Lib_Storage::storeFile($storageType, $tmpFile, $uuid);
        $imageRow = $this->_table->createRow(array(
          'id' => $uuid,
          'storageType' => $storageType,
        ));
        try {
          $imageRow->save();
        } catch (Zend_Db_Table_Exception $e) {
          throw new Lib_Exception(
            'Could not save image DB entry',
            Api_ErrorCodes::IMAGE_DB_SAVE_FAILURE);
        }
        $thisFileOutput['key'] = $uuid;
      } catch (Lib_Exception $e) {
        Lib_Storage::cleanUpFiles($storageType, $uuid);
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


  }

  /*
   * All other methods are forbiddem
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
    $this->_unauthorised();
  }

  public function getAction()
  {
    $this->_unauthorised();
  }
}