<?php

use RicardoFiorani\Matcher\VideoServiceMatcher;

class Lib_VideoScraper
{
  public function __construct($videoUrl)
  {
    $this->_vsm = new VideoServiceMatcher();
    $this->_videoUrl = $videoUrl;
    $this->_table = new Api_Image();
  }

  public function saveThumbs($storageType, $objectId)
  {
    $video = $this->_vsm->parse($this->_videoUrl);
    $thumbUrl = $video->getLargeThumbnail();

    $tmpFile = tempnam(GLOBAL_UPLOAD_TMPDIR, 'thumb');
    $this->_writeFileToDisk($tmpFile, $thumbUrl);

    switch ($storageType) {
      case Lib_Storage::TYPE_LOCAL:
        $imageRow = $this->_saveLocalThumbs($tmpFile, $storageType, $objectId);
        @unlink($tmpFile);
        return $imageRow;
        break;
      default:
        throw new Api_Exception_BadRequest(
          "Could not save thumb with storage type '$storageType'",
          Api_ErrorCodes::IMAGE_BAD_STORAGE_TYPE);
        break;
    }
  }

  protected function _saveLocalThumbs($tmpFile, $storageType, $objectId)
  {
    try {
      list($w, $h) = Lib_Storage::storeThumbs($storageType, $tmpFile, $objectId);
    } catch (Lib_Exception_Media_Photo_Mime $e) {
      throw new Api_Exception_BadRequest($e->getMessage(), Api_ErrorCodes::IMAGE_BAD_MIME);
    }
    $imageRow = $this->_table->createRow(array(
      'id' => $objectId,
      'imageType' => Api_Image::IMAGE_TYPE_THUMB,
      'storageType' => $storageType,
      'width' => $w,
      'height' => $h,
    ));
    try {
      $imageRow->save();
    } catch (Zend_Db_Exception $e) {
      Lib_Storage::cleanUpFiles($storageType, $this->_videoKey);
      throw new Api_Exception_BadRequest(
        'Could not save image DB entry',
        Api_ErrorCodes::IMAGE_DB_SAVE_FAILURE);
    }
    return $imageRow;
  }

  protected function _writeFileToDisk($tmpFile, $thumbUrl)
  {
    if (APPLICATION_ENV == 'test') {
      $thumbUrl = TEST_UPLOAD_FILE;
    }

    file_put_contents($tmpFile, file_get_contents($thumbUrl));
  }
}