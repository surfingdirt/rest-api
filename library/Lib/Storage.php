<?php
class Lib_Storage
{
  const SMALL = 'small';
  const MEDIUM = 'medium';
  const LARGE = 'large';

  const TYPE_LOCAL = 0;

  public static $config = array(
    self::TYPE_LOCAL =>
      array(
      'images' => array(
        self::SMALL  => array('width' => 800, 'height' => 450, 'suffix' => '_s'),
        self::MEDIUM => array('width' => 1280, 'height' => 720, 'suffix' => '_m'),
        self::LARGE  => array('width' => 1920, 'height' => 1080, 'suffix' => '_l'),
      ),
      'thumbs' => array(
        self::SMALL  => array('width' => 240, 'height' => 135, 'suffix' => '_ts'),
        self::MEDIUM => array('width' => 400, 'height' => 225, 'suffix' => '_tm'),
        self::LARGE  => array('width' => 640, 'height' => 360, 'suffix' => '_tl'),
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
   * @return array
   */
  public static function storeFile($storageType, $tmpFile, $id)
  {
    $savedFiles = array();

    $config = self::$config[$storageType];
    if (!$config) {
      throw new Lib_Exception("No config found for storage tyoe '$storageType'");
    }

    $photoFile = new File_Photo($tmpFile);
    $destination = $config['path'] . DIRECTORY_SEPARATOR . $id;
    if ($extension = $photoFile->getExtensionForSubType()) {
      $destination .= '.' . $extension;
    }
    if (!$photoFile->moveUploadedFile($destination)) {
      throw new Lib_Exception("Could not move file '$tmpFile' to '$destination'");
    }
    $filePath = $photoFile->getFullPath();
    $savedFiles[] = $filePath;

    // Large JPG
    $morePaths = $photoFile->generateResizedVersions(
      $config['images'],
      $config['path'],
      Media_Item_Photo::SUBTYPE_JPG);
    array_merge($savedFiles, $morePaths);

    // JPG Thumbs
    $morePaths = $photoFile->generateResizedVersions(
      $config['thumbs'],
      $config['path'],
      Media_Item_Photo::SUBTYPE_JPG);
    array_merge($savedFiles, $morePaths);

    if (function_exists('imagewebp')) {
      // Large WebP
      $morePaths = $photoFile->generateResizedVersions(
        $config['images'],
        $config['path'],
        Media_Item_Photo::SUBTYPE_WEBP);
      array_merge($savedFiles, $morePaths);

      // WebP Thumbs
      $morePaths = $photoFile->generateResizedVersions(
        $config['thumbs'],
        $config['path'],
        Media_Item_Photo::SUBTYPE_WEBP);
      array_merge($savedFiles, $morePaths);
    }

    return $savedFiles;
  }
}