<?php

class Api_Media_Accessor extends Api_Data_Accessor
{
  /**
   * List of fields in a form that will never match
   * a field in the data DB table.
   * Example: 'submit'
   *
   * @var array
   */
  protected $_disregardUpdates = array(
    'tags',
    'submit',
    'skipAutoFields',
    'longitude',
    'latitude',
    'zoom',
    'yaw',
    'pitch',
    'mapType',
    'token',
    'media',
    'users',
    'path',
    'locationFlag'
  );

  public $publicReadAttributes = array(
    'id',
    'title',
    'description',
    'mediaType',
    'mediaSubType',
    'album',
    'vendorKey',
    'date',
    'submitter',
    'lastEditor',
    'lastEditionDate',
    'status',
    'imageId',
    'users',
//    'dpt',
//    'spot',
//    'trick',
//    'longitude',
//    'latitude',
    'uri',
    'width',
    'height',
//    'author',
  );

  public $memberCreateAttributes = array(
    'title' => 'title',
    'description' => 'description',
//    'status' => 'status',
//    'dpt' => 'dpt',
//    'spot' => 'spot',
//    'trick' => 'trick',
//    'longitude' => 'longitude',
//    'latitude' => 'latitude',
    'albumId' => 'albumId',
    'mediaType' => 'mediaType',
    'vendorKey' => 'vendorKey',
    'imageId' => 'imageId',
    'mediaSubType' => 'mediaSubType',
    'storageType' => 'storageType',

    // These are autogenerated and user input will be discarded:
    'width' => 'width',
    'height' => 'height',

//    'author' => 'author',
  );

  public $ownWriteAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'imageId' => 'imageId',
//    'dpt' => 'dpt',
//    'spot' => 'spot',
//    'trick' => 'trick',
//    'longitude' => 'longitude',
//    'latitude' => 'latitude',
//    'author' => 'author',
  );

  public $adminWriteAttributes = array(
    'status' => 'status',
    'albumId' => 'albumId',
    'vendorKey' => 'vendorKey',
  );

  public $forbiddenWriteAttributes = array(
    'mediaType' => 'mediaType',
    'storageType' => 'storageType',
  );

  public function getUpdateAttributes($object)
  {
    $attrs = parent::getUpdateAttributes($object);
    if ($object->mediaType == Media_Item::TYPE_VIDEO) {
      $attrs['vendorKey'] = 'vendorKey';
      $attrs['mediaSubType'] = 'mediaSubType';
    }
    return $attrs;
  }

  /**
   * Performs a read operation
   */
  public function getObjectData($neutralObject, $action = 'list')
  {
    $object = Api_Media_Factory::buildItemByIdAndMediaType($neutralObject->getId(), $neutralObject->getMediaType());
    $attributes = $this->getReadAttributes($object);

    $ret = array();
    foreach ($attributes as $attr) {
      $ret = $this->_addEntriesForAttribute($attr, $object, $ret);
    }

    return $ret;
  }

  /**
   * Creates an object for a POST operation
   */
  public function createObjectWithData($object, $data)
  {
    if (!$object->isCreatableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised('Access unauthorised for user ' . $this->_user->getId());
    }

    $form = $object->getForm($this->_user, $this->_acl, null, $data);
    if (!$form->isValid($data)) {
      $errors = $form->getNonEmptyErrors();
      return array(null, $errors);
    }

    $object->id = Utils::uuidV4();
    if ($object->mediaType == Media_Item::TYPE_PHOTO) {
      $data = array_merge($data, $this->_getPhotoData($data['imageId']));
    } else {
      $scraper = new Lib_VideoScraper($data['mediaSubType'], $data['vendorKey']);
      $imageId = Utils::uuidV4();
      $thumbRow = $this->_saveVideoThumbs($scraper, $imageId);
      $data = array_merge($data, $this->_getVideoData($thumbRow));
    }
    $this->_save($object, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
    return array($object->getId(), null);
  }

  public function updateObjectWithData($object, $data)
  {
    $attributes = $this->getUpdateAttributes($object);
    $form = $object->getForm($this->_user, $this->_acl);
    $data = array_merge($form->populateFromDatabaseData($object->toArray()), $data);
    if (!$form->isValid($data)) {
      $errors = $form->getNonEmptyErrors();
      return $errors;
    }
    if ($this->_shouldUpdateImage($object, $data)) {
      // Remove the old image
      Api_Image::cleanupById($object->storageType, $object->imageId);

      if ($object->mediaType == Media_Item::TYPE_PHOTO) {
        $data = array_merge($data, $this->_getPhotoData($data['imageId']));
      } else {
        $scraper = new Lib_VideoScraper($data['mediaSubType'], $data['vendorKey']);
        $imageId = Utils::uuidV4();
        $thumbRow = $this->_saveVideoThumbs($scraper, $imageId);
        $data = array_merge($data, $this->_getVideoData($thumbRow));
      }
    }

    $this->_save($object, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
    return array();
  }

  protected function _shouldUpdateImage($object, $data)
  {
    if ($object->getMediaType() == Media_Item::TYPE_PHOTO) {
      if ($object->imageId != $data['imageId']) {
        return true;
      }
    } else {
      if ($object->vendorKey != $data['vendorKey']) {
        return true;
      }
    }
    return false;
  }

  protected function _save(Data_Row $dataRow, Media_Item_Form $form, array $data, User_Row $user, Lib_Acl $acl, array $disregardUpdates = array())
  {
    if (empty($data['status'])) {
      $data['status'] = Data::VALID;
    }

    // Update of fields
    $attributes = $this->getCreateAttributes($dataRow);
    foreach ($attributes as $key => $value) {
      if (in_array($key, $disregardUpdates)) {
        continue;
      }
      if (isset($data[$key])) {
        $dataRow->$key = $data[$key];
      }
    }

    // Saving
    $skipAutomaticEditionFields = false;
    $dataRow->save($skipAutomaticEditionFields);
    if (empty($dataRow->id)) {
      throw new Lib_Exception("Could not save data");
    }

    if (isset($data['users'])) {
      $table = new Media_Item_Users();
      $table->updateUsers($dataRow->id, $data['users']);
    }

    $dataRow->clearCache();
    return $dataRow->id;
  }

  protected function _getPhotoData($imageId)
  {
    $table = new Api_Image();
    $image = $table->find($imageId)->current();
    return array(
      'width' => $image->width,
      'height' => $image->height,
      'mediaSubType' => Media_Item_Photo::SUBTYPE_IMG,
    );
  }

  protected function _saveVideoThumbs(Lib_VideoScraper $scraper, $objectId)
  {
    $imageRow = $scraper->saveThumbs(Lib_Storage::TYPE_LOCAL, $objectId);
    return $imageRow;
  }

  protected function _getVideoData($imageRow)
  {
    return array(
      'imageId' => $imageRow->getId(),
      // width and height are irrelevant to video embeds.
      'width' => 0,
      'height' => 0,
    );
  }

  protected function _getMediaFileName($title, $uniqid)
  {
    $return = Utils::cleanStringForFilename($title);
    if (APPLICATION_ENV != 'test') {
      // We usually want unique names, but for testing, predictable is better
      $return .= '_' . $uniqid;
    }
    return $return;
  }

  /**
   * Deletes file associated to a photo
   *
   * @param Media_Item_Photo_Row $photo
   */
  protected function _cleanUpPhotoFiles(Media_Item_Photo_Row $photo, $destination = null)
  {
    // Delete a possible thumbnail
    $thumb = APP_MEDIA_DIR . DIRECTORY_SEPARATOR . $photo->getThumbnailURI();
    if (is_file($thumb)) {
      Globals::getLogger()->deletes("Cleaning up after upload of photo: deleted thumbnail file '$thumb'", Zend_Log::INFO);
      unlink($thumb);
    }

    // Delete a possible file
    $file = APP_MEDIA_DIR . DIRECTORY_SEPARATOR . $photo->getURI(false);
    if (is_file($file)) {
      Globals::getLogger()->deletes("Cleaning up after upload of photo: deleted photo file '$file'", Zend_Log::INFO);
      unlink($file);
    }

    // Delete a possible file without an extension
    if (!empty($destination) && is_file($destination)) {
      Globals::getLogger()->deletes("Cleaning up after upload of photo: deleted photo file without an extension '$destination'", Zend_Log::INFO);
      unlink($destination);
    }

    // Delete a possible raw media stored
    $rawMediaFile = APP_MEDIA_DIR_RAW . DIRECTORY_SEPARATOR . $photo->getURI(false);
    if (is_file($rawMediaFile)) {
      Globals::getLogger()->deletes("Cleaning up after upload of photo: deleted raw photo file '$rawMediaFile'", Zend_Log::INFO);
      unlink($rawMediaFile);
    }
  }

  /**
   * Removes all trace of the media from the database
   *
   * @param Media_Item_Row $media
   */
  protected function _cleanUpMedia(Media_Item_Row $media)
  {
    if ($media->id) {
      $media->delete();
      // Erase any user tagged in this media
      $mediaUsersTable = new Media_Item_Users();
      $mediaUsersTable->delete(array('id' => $media->id));
    }
  }

  protected function _populateLocationFormElements(Data_Row $dataRow, array $postData)
  {
    $return = array();
    if ($dataRow->hasLocation()) {
      $location = $dataRow->getLocation();
      if (empty($location)) {
        return $return;
      }
      $return['longitude'] = $location->longitude;
      $return['latitude'] = $location->latitude;
      $return['zoom'] = $location->zoom;
      $return['yaw'] = $location->yaw;
      $return['pitch'] = $location->pitch;
      $return['mapType'] = $location->mapType;
    }

    if (array_key_exists('longitude', $postData)) {
      $return['longitude'] = $postData['longitude'];
    }
    if (array_key_exists('latitude', $postData)) {
      $return['latitude'] = $postData['latitude'];
    }
    if (array_key_exists('zoom', $postData)) {
      $return['zoom'] = $postData['zoom'];
    }
    if (array_key_exists('yaw', $postData)) {
      $return['yaw'] = $postData['yaw'];
    }
    if (array_key_exists('pitch', $postData)) {
      $return['pitch'] = $postData['pitch'];
    }
    if (array_key_exists('mapType', $postData)) {
      $return['mapType'] = $postData['mapType'];
    }

    return $return;
  }
}