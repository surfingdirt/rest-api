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

    $fileErrors = array();
    $savedUUIDs = array();
    $clearFilesList = array();

    foreach ($_FILES['files']['tmp_name'] as $i => $tmpFile) {
      try {
        if ($_FILES['files']['error'][$i]) {
          throw new Lib_Exception("File upload failed for file $i - maybe too big");
        }

        $uuid = Utils::uuidV4();
        $currentSavedFiles = Lib_Storage::storeFile($storageType, $tmpFile, $uuid);
        array_merge($clearFilesList, $currentSavedFiles);
        $savedUUIDs[] = $uuid;
      } catch (Lib_Exception $e) {
        // TODO: send an actual code, and translate the message.
        $fileErrors[$i] = array('code' => $e->getCode(), 'message' => $e->getMessage());
      }
    }

    if (sizeof($fileErrors) > 0) {
      foreach ($clearFilesList as $toDelete) {
        $file = new File($toDelete);
        $status = $file->delete();
        if (!$status) {
          Globals::getLogger()->error("Could not delete file: ". $file->getFullPath());
        }
      }
      $this->view->output = array('errors' => $fileErrors);
      return;
    }

    $this->view->output = array('saved' => $savedUUIDs);
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