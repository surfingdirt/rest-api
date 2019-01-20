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
      throw new Exception("Could not find user '$userId'");
    }
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
      throw new Api_Exception_BadRequest('No type given');
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
          throw new Lib_Exception("Upload failed");
        }

        Lib_Storage::storeFile($storageType, $tmpFile, $uuid);
        $thisFileOutput['key'] = $uuid;
      } catch (Lib_Exception $e) {
        // TODO: send an actual code, and translate the message.
        Lib_Storage::cleanUpFiles($storageType, $uuid);
        $thisFileOutput['error'] = array('code' => $e->getCode(), 'message' => $e->getMessage());
      }

      $output[] = $thisFileOutput;
    }

    $this->view->output = $output;
  }

  public function deleteAction()
  {
    // Only owner and editor/admin have access
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