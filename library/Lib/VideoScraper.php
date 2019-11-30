<?php

class Lib_VideoScraper
{
  public function __construct($mediaSubType, $vendorKey)
  {
    $this->_mediaSubType = $mediaSubType;
    $this->_vendorKey = $vendorKey;
    $this->_videoUrl = VideoUrlBuilder::buildUrl($mediaSubType, $vendorKey);
    $this->_table = new Api_Image();
    $this->_thumbUrl = null;
  }

  protected function _getThumbUrl()
  {
    if ($this->_thumbUrl) {
      return $this->_thumbUrl;
    }

    $thumbUrl = null;

    if (APPLICATION_ENV == 'test') {
      $headers = apache_request_headers();
      if (isset($headers['X-localVideoThumb'])) {
        $thumbUrl = $headers['X-localVideoThumb'];
      }
    }

    if (!$thumbUrl) {
      switch ($this->_mediaSubType) {
        case Media_Item_Video::SUBTYPE_YOUTUBE:
          $id = $this->_vendorKey;
          $thumbUrl = "https://i.ytimg.com/vi/${id}/hqdefault.jpg";
          break;
        default:
          $graph = Lib_OG::fetch($this->_videoUrl);
          if ($graph->image) {
            $thumbUrl = $graph->image;
          }
          break;
      }
    }

    return $thumbUrl;
  }

  public function setThumbUrl($thumbUrl) {
    $this->_thumbUrl = $thumbUrl;
  }

  public function saveThumbs($storageType, $objectId)
  {
    $thumbUrl = $this->_getThumbUrl();
    if (!$thumbUrl) {
      $thumbUrl = BASE_PATH.DEFAULT_VIDEO_THUMB_PATH;
    }
    $tmpFile = tempnam(GLOBAL_UPLOAD_TMPDIR, 'thumb');
    $this->_writeFileToDisk($tmpFile, $thumbUrl);

    switch ($storageType) {
      case Lib_Storage::TYPE_LOCAL:
        $imageRow = $this->_saveLocalThumbs($tmpFile, $storageType, $objectId);
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
    file_put_contents($tmpFile, file_get_contents($thumbUrl));
  }
}