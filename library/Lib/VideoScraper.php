<?php

use RicardoFiorani\Matcher\VideoServiceMatcher;

class Lib_VideoScraper
{
  public function __construct($videoUrl, $objectId)
  {
    $this->_vsm = new VideoServiceMatcher();
    $this->_videoUrl = $videoUrl;
    $this->_objectId = $objectId;
    $this->_table = new Api_Image();
  }

  public function saveThumbs($storageType)
  {
    $video = $this->_vsm->parse($this->_videoUrl);
    $thumbUrl = $video->getLargeThumbnail();

    $tmpFile = tempnam(GLOBAL_UPLOAD_TMPDIR, 'thumb');
    file_put_contents($tmpFile, file_get_contents($thumbUrl));

    switch ($storageType) {
      case Lib_Storage::TYPE_LOCAL:
        $imageRow = $this->_saveLocalThumbs($tmpFile, $storageType);
        return $imageRow;
        break;
      default:
        throw new Api_Exception_BadRequest(
          "Could not save thumb with storage type '$storageType'",
          Api_ErrorCodes::IMAGE_BAD_STORAGE_TYPE);
        break;
    }
  }

  protected function _saveLocalThumbs($tmpFile, $storageType)
  {
    try {
      list($w, $h) = Lib_Storage::storeThumbs($storageType, $tmpFile, $this->_objectId);
    } catch (Lib_Exception_Media_Photo_Mime $e) {
      throw new Api_Exception_BadRequest($e->getMessage(), Api_ErrorCodes::IMAGE_BAD_MIME);
    }
    $imageRow = $this->_table->createRow(array(
      'id' => $this->_objectId,
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
}