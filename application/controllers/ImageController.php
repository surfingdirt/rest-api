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

    if (!isset($_FILES) || !isset($_FILES['files'])) {
      throw new Api_Exception_BadRequest();
    }

    $fileErrors = array();
    $savedFiles = array();
    $clearFilesList = array();

    foreach ($_FILES['files']['tmp_name'] as $i => $tmpFile) {
      try {
        if (!$_FILES['files'][$i]['size']) {
          throw new Lib_Exception("No file found, too big?");
        }

        $uuid = Utils::uuidV4();
        $photoFile = new File_Photo($tmpFile);
        $destination = PUBLIC_FILES_DIR . DIRECTORY_SEPARATOR . $uuid;
        if ($extension = $photoFile->getExtensionForSubType()) {
          $destination .= '.' . $extension;
        }

        if (!$photoFile->moveUploadedFile($destination)) {
          throw new Lib_Exception("Could not move file '$tmpFile' to '$destination'");
        }
        $filePath = $photoFile->getFullPath();
        $clearFilesList[] = $filePath;
        $savedFiles[] = $filePath;

        // Large versions in both JPG and WEBP (if available)
        $standardJpgPaths = $photoFile->generateResizedVersions(
          Media_Item_Photo::$standardSizes,
          PUBLIC_FILES_DIR,
          Media_Item_Photo::SUBTYPE_JPG);
        if (function_exists('imagewebp')) {
          $standardWebpPaths = $photoFile->generateResizedVersions(
            Media_Item_Photo::$standardSizes,
            PUBLIC_FILES_DIR,
            Media_Item_Photo::SUBTYPE_WEBP);
        } else {
          $standardWebpPaths = array();
        }
        // Thumbnail versions in both JPG and WEBP (if available)
        $thumbJpgPaths = $photoFile->generateResizedVersions(
          Media_Item::$thumbSizes,
          PUBLIC_FILES_DIR,
          Media_Item_Photo::SUBTYPE_JPG);
        if (function_exists('imagewebp')){
          $thumbWebPaths = $photoFile->generateResizedVersions(
            Media_Item::$thumbSizes, PUBLIC_FILES_DIR,
            Media_Item_Photo::SUBTYPE_WEBP);
        } else {
          $thumbWebPaths = array();
        }
        array_merge($clearFilesList, $standardJpgPaths, $standardWebpPaths, $thumbJpgPaths, $thumbWebPaths);

      } catch (Lib_Exception $e) {
        // TODO: send an actual code, and translate the message.
        $fileErrors[$i] = array('code' => $e->getCode(), 'message' => $e->getMessage());
      }
    }

    if (sizeof($fileErrors) > 0) {
      // TODO: delete all files in $cleanupFileList
      $this->view->output = array('errors' => $fileErrors);
      return;
    }

    /*
     * - recupere les fichiers dans le body
     * - parse et checke que ce sont des fichiers d'image. sinon retourne un tableau d'erreurs (Efface fichiers temps?)
     * - genere un id unique pour les fichiers
     * - sauve les fichiers au bon endroit. nettoie et retourne erreurs en cas de probleme
     * - genere les miniatures. nettoie et retourne erreurs en cas de probleme.
     * - renvoie un object contenant les url et dimensions de fichiers.
     */
    $this->view->output = array('files' => $savedFiles);
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