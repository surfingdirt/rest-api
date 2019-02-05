<?php

class Lib_Storage
{
  const SMALL = 'small';
  const MEDIUM = 'medium';
  const LARGE = 'large';

  const FILENAME = 'img';

  const TYPE_LOCAL = 0;

  public static $validTypes = array(self::TYPE_LOCAL);

  public static $config = array(
    self::TYPE_LOCAL =>
      array(
        'images' => array(
          self::SMALL => array('width' => 800, 'height' => 450, 'suffix' => '_s'),
          self::MEDIUM => array('width' => 1280, 'height' => 720, 'suffix' => '_m'),
          self::LARGE => array('width' => 1920, 'height' => 1080, 'suffix' => '_l'),
        ),
        'thumbs' => array(
          self::SMALL => array('width' => 240, 'height' => 135, 'suffix' => '_ts'),
          self::MEDIUM => array('width' => 400, 'height' => 225, 'suffix' => '_tm'),
          self::LARGE => array('width' => 640, 'height' => 360, 'suffix' => '_tl'),
        ),
        'path' => PUBLIC_FILES_DIR
      )
  );

  /**
   * Performs file storage and resizing operations
   * @param $storageType
   * @param $tmpFile
   * @param $id
   * @throws Lib_Exception
   * @throws Lib_Exception_Media_Photo_Mime
   * @return void
   */
  public static function storeFile($storageType, $tmpFile, $id)
  {
    $config = self::$config[$storageType];
    if (!$config) {
      throw new Lib_Exception(
        "No config found for storage type '$storageType'",
        Api_ErrorCodes::IMAGE_BAD_TYPE);
    }

    switch ($storageType) {
      case self::TYPE_LOCAL:
        return self::storeLocalFile($config, $tmpFile, $id);
      default:
        throw new Lib_Exception(
          "Storage method not implemented for storage type '$storageType'",
          Api_ErrorCodes::IMAGE_STORAGE_METHOD_NOT_IMPLEMENTED);
        break;
    }
  }

  /**
   * Performs file storage and resizing operations
   * @param $storageType
   * @param $tmpFile
   * @param $id
   * @throws Lib_Exception
   * @throws Lib_Exception_Media_Photo_Mime
   * @return void
   */
  public static function storeThumbs($storageType, $tmpFile, $id)
  {
    $config = self::$config[$storageType];
    if (!$config) {
      throw new Lib_Exception(
        "No config found for storage type '$storageType'",
        Api_ErrorCodes::IMAGE_BAD_TYPE);
    }

    switch ($storageType) {
      case self::TYPE_LOCAL:
        return self::storeLocalThumbs($config, $tmpFile, $id);
      default:
        throw new Lib_Exception(
          "Storage method not implemented for storage type '$storageType'",
          Api_ErrorCodes::IMAGE_STORAGE_METHOD_NOT_IMPLEMENTED);
        break;
    }
  }

  public static function storeLocalFile($config, $tmpFile, $id)
  {
    $folder = self::_getFolderForFile($config['path'], $id);
    if (!mkdir($folder)) {
      throw new Lib_Exception(
        "Could not create folder to store file",
        Api_ErrorCodes::IMAGE_FOLDER_CREATION_FAILED);
    }

    $photoFile = new File_Photo($tmpFile);
    if (!$extension = $photoFile->getExtensionForSubType()) {
      throw new Lib_Exception(
        "Could not determine file extensions",
        Api_ErrorCodes::IMAGE_NO_EXTENSION);
    }

    $destination = $folder . DIRECTORY_SEPARATOR . self::FILENAME . '.' . $extension;
    if (!$photoFile->moveUploadedFile($destination)) {
      throw new Lib_Exception(
        "Could not move file '$tmpFile' to '$destination'",
        Api_ErrorCodes::IMAGE_COULD_NOT_MOVE_UPLOADED_FILE);
    }

    // Large JPG
    $photoFile->generateResizedVersions(
      $config['images'],
      $folder,
      Media_Item_Photo::SUBTYPE_JPG);

    // JPG Thumbs
    $photoFile->generateResizedVersions(
      $config['thumbs'],
      $folder,
      Media_Item_Photo::SUBTYPE_JPG);

    if (function_exists('imagewebp')) {
      // Large WebP
      $photoFile->generateResizedVersions(
        $config['images'],
        $folder,
        Media_Item_Photo::SUBTYPE_WEBP);

      // WebP Thumbs
      $photoFile->generateResizedVersions(
        $config['thumbs'],
        $folder,
        Media_Item_Photo::SUBTYPE_WEBP);
    }

    $photoFile->limitDimensions(
      GLOBAL_DEFAULT_IMG_MAX_WIDTH,
      GLOBAL_DEFAULT_IMG_MAX_HEIGHT,
      false);

    return array($photoFile->getWidth(), $photoFile->getHeight());
  }

  public static function storeLocalThumbs($config, $tmpFile, $id)
  {
    $folder = self::_getFolderForFile($config['path'], $id);
    if (!mkdir($folder)) {
      throw new Lib_Exception(
        "Could not create folder to store file",
        Api_ErrorCodes::IMAGE_FOLDER_CREATION_FAILED);
    }

    $photoFile = new File_Photo($tmpFile);
    if (!$extension = $photoFile->getExtensionForSubType()) {
      throw new Lib_Exception(
        "Could not determine file extensions",
        Api_ErrorCodes::IMAGE_NO_EXTENSION);
    }
    $photoFile->move($folder);
    $photoFile->rename(self::FILENAME . '.' . $extension, true);

    // JPG Thumbs
    $photoFile->generateResizedVersions(
      $config['thumbs'],
      $folder,
      Media_Item_Photo::SUBTYPE_JPG);

    if (function_exists('imagewebp')) {
      // WebP Thumbs
      $photoFile->generateResizedVersions(
        $config['thumbs'],
        $folder,
        Media_Item_Photo::SUBTYPE_WEBP);
    }

    $output = array($photoFile->getWidth(), $photoFile->getHeight());
    $photoFile->delete();
    return $output;
  }

  public static function cleanUpFiles($storageType, $id)
  {
    $config = self::$config[$storageType];
    if (!$config) {
      return;
    }

    switch ($storageType) {
      case self::TYPE_LOCAL:
        $folder = self::_getFolderForFile($config['path'], $id);
        self::_removeDirectory($folder);
        break;
      default:
        throw new Lib_Exception(
          "Cleanup not implemented for storage type '$storageType'",
          Api_ErrorCodes::IMAGE_CLEANUP_METHOD_NOT_IMPLEMENTED);
        break;
    }
  }

  protected static function _getFolderForFile($path, $id)
  {
    return $path . DIRECTORY_SEPARATOR . $id;
  }

  private static function _removeDirectory($path)
  {
    Globals::getLogger()->log("Deleting '$path'");
    $files = glob($path . '/*');
    foreach ($files as $file) {
      is_dir($file) ? self::_removeDirectory($file) : @unlink($file);
    }
    @rmdir($path);
  }
}