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
    'riders',
    'path',
    'locationFlag'
  );

  public $publicReadAttributes = array(
    'id',
    'title',
    'description',
    'date',
    'submitter',
    'lastEditor',
    'lastEditionDate',
    'status',
    'dpt',
    'spot',
    'trick',
    'longitude',
    'latitude',
    'album',
    'mediaType',
    'uri',
    'width',
    'height',
    'size',
    'mediaSubType',
    'thumbnailUri',
    'thumbnailWidth',
    'thumbnailHeight',
    'thumbnailSubType',
    'author',
    'externalKey'
  );

  public $memberCreateAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'status' => 'status',
    'dpt' => 'dpt',
    'spot' => 'spot',
    'trick' => 'trick',
    'longitude' => 'longitude',
    'latitude' => 'latitude',
    'albumId' => 'albumId',
    'mediaType' => 'mediaType',
    'author' => 'author',
  );

  public $ownWriteAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'dpt' => 'dpt',
    'spot' => 'spot',
    'trick' => 'trick',
    'longitude' => 'longitude',
    'latitude' => 'latitude',
    'author' => 'author',
  );

  public $adminWriteAttributes = array(
    'status' => 'status',
    'albumId' => 'albumId',
    'mediaType' => 'mediaType',
  );

  /**
   * Performs a read operation
   */
  public function getObjectData($neutralObject, $action = 'list')
  {
    /**
     * This is a shitty architecture:
     * you need to know the type of media (photo/video) in order
     * to instantiate it correctly (Media_Item_Photo_Row vs
     * Media_Item_Video_Row). But you don't know the type
     * until after it is instantiated.
     *
     * Workaround: load the object normally in the controller,
     * and once we get it here, fork depending on the albumType to
     * instantiate the correct object, in order to read it.
     */
    if ($neutralObject->mediaType == Media_Item::TYPE_PHOTO) {
      $table = new Media_Item_Photo();
    } else {
      $table = new Media_Item_Video();
    }

    $object = $table->find($neutralObject->getId())->current();

    $attributes = $this->getReadAttributes($object);
    $location = $object->getLocation();

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
    // TODO: check that the given (or default) album qualifies to receive this media
    $errors = array();
    $form = $object->getForm($this->_user, $this->_acl, null, $data);
    if (!$form->isValid($data)) {
      $rawErrors = $form->getErrors();
      foreach ($rawErrors as $name => $err) {
        if (!empty($err)) {
          $errors[$name] = $err;
        }
      }
    } else {
      if ($object->mediaType == Media_Item::TYPE_PHOTO) {
        $data = array_merge($data, $this->_savePhotoFiles($object, $form, APP_MEDIA_DIR, uniqid()));
      } else {
        $data = array_merge($data, $this->_getVideoAttributes($object, $form->media));
      }
      if (empty($data['status'])) {
        $data['status'] = Data::VALID;
      }
      $this->_save($object, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
      $table = new Media_Item_Riders();
      $table->insertRiders($object->id, $form->riders->getNames());
    }

    return array($object->getId(), $errors);
  }

  /**
   * Updates an object for a PUT operations
   */
  public function updateObjectWithData($object, $data)
  {
    $attributes = $this->getUpdateAttributes($object);
    //error_log('attributes ' . var_export($attributes, true));
    //error_log('data before form' . var_export($data, true));
    $log = '';

    $errors = array();
    switch ($object->mediaType) {
      default:
      case Media_Item::TYPE_PHOTO:
        $form = new Api_Media_Form_Photo($object, $this->_user, $this->_acl);
        $form->setName('PhotoForm');
        break;
      case Media_Item::TYPE_VIDEO:
        $form = new Api_Media_Form_Video($object, $this->_user, $this->_acl);
        $form->setName('VideoForm');
        break;
    }

    $formattedData = array_merge($object->toArray(), $data, $this->_populateLocationFormElements($object, $data));
    $form->populateFromDatabaseData($formattedData);
    $dbData = $form->getFormattedValuesForDatabase();
    // TODO: if given, check that the album qualifies to receive this media
    if (!$form->isValid($data)) {
      $rawErrors = $form->getErrors();
      foreach ($rawErrors as $name => $err) {
        if (!empty($err)) {
          $errors[$name] = $err;
        }
      }
    } else {
      if ($object->mediaType == Media_Item::TYPE_PHOTO) {
        $data = array_merge($data, $this->_savePhotoFiles($object, $form, APP_MEDIA_DIR, uniqid()));
      } else {
        $data = array_merge($data, $this->_getVideoAttributes($object, $form->media));
      }
      /*
      $formattedData = $form->getFormattedValuesForDatabase();
      foreach($attributes as $attrFormName => $attrDBName){
        $this->_updateKey($object, $attrFormName, $attrDBName, $data, $formattedData);
      }
      */
      //error_log($log);
      $this->_save($object, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
    }
    return $errors;
  }

  /**
   * Saves a Data_Row in database, setting its data from the submitted form
   *
   * @param Data_Row $data
   * @param Data_Form $form
   * @param User_Row $user
   * @param Lib_Acl $acl
   * @param array $disregardUpdates
   * @return int
   * @throws Lib_Exception
   */
  protected function _save(Data_Row $dataRow, Data_Form $form, array $data, User_Row $user, Lib_Acl $acl, array $disregardUpdates = array())
  {
    $reflection = new ReflectionClass ($dataRow);

    // Update of fields
    foreach ($data as $key => $value) {
      if (in_array($key, $disregardUpdates)) {
        continue;
      }
      $dataRow->$key = $value;
    }

    // Tags
    $tags = $form->getElement('tags');
    if ($tags) {
      $dataRow->setTags($tags->getValue());
    }

    // Saving
    $skipAutomaticEditionFields = false;
    $dataRow->save($skipAutomaticEditionFields);
    if (empty($dataRow->id)) {
      throw new Lib_Exception("Could not save data");
    }

    // Location
    if ($reflection->implementsInterface('Data_Row_LocationInterface')) {
      $this->_manageLocation($dataRow, $data);
    }

    $dataRow->clearCache();
    return $dataRow->id;
  }

  /**
   * Takes care of inserting, updating and deleting of Location
   *
   * @param Data_Row|User_Row $object
   * @param array $data
   */
  protected function _manageLocation($object, array $data)
  {
    $location = $object->getLocation();
    if ($location && (isset($data['longitude']) && is_null($data['longitude']) ||
        isset($data['latitude']) && is_null($data['latitude']))) {
      // Deleting an existing location
      $location->delete();
      return;
    }

    if (empty($data['longitude']) || empty($data['latitude'])) {
      return;
    }

    if (!$location) {
      $table = new Location();
      $location = $table->fetchNew();
    }

    // Creating/updating
    $location->longitude = $data['longitude'];
    $location->latitude = $data['latitude'];
    /*
    $location->zoom = $data['zoom'];
    $location->yaw = $data['yaw'];
    $location->pitch = $data['pitch'];
    $location->mapType = $data['mapType'];
    */
    $location->status = Data::VALID;
    $location->itemId = $object->getId();
    $location->itemType = $object->getItemType();
    $location->save();
  }

  /**
   * Returns information about the video uri, thumbnail dimensions in order to save to database
   *
   * @param Media_Item_Video_Row $video
   * @param Lib_Form_Element_Video $videoElement
   * @return array
   * @throws Lib_Exception_Media
   */
  protected function _getVideoAttributes(Media_Item_Video_Row $video, Lib_Form_Element_Video $videoElement)
  {
    $value = $videoElement->getValue();
    if ($video->getId() && empty($value)) {
      return array();
    }

    $regex = Media_Item_Video::getCleanVideoCodeRegex();

    $matches = null;
    $matchCount = preg_match_all($regex, $value, $matches);
    if (!$matchCount) {
      throw new Lib_Exception_Media("Impossible to get data out of the video regex: value='$value', regex='$regex', videoId:'$video->id'");
    }
    $return = array();
    switch ($matches[1][0]) {
      case Media_Item_Video::SUBTYPE_YOUTUBE:
        if (APPLICATION_ENV == 'test') {
          $return['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_YOUTUBE_THUMBNAIL;
          $return['thumbnailUri'] = 'fakeUri';
          $return['thumbnailWidth'] = '320';
          $return['thumbnailHeight'] = '240';
        } else {
          $youtube = new Zend_Gdata_YouTube(null, YOUTUBE_APPLICATION_ID, YOUTUBE_CLIENT_ID, YOUTUBE_API_KEY);
          $entry = $youtube->getVideoEntry($matches[4][0]);
          $thumbnails = $entry->getVideoThumbnails();
          $return['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_YOUTUBE_THUMBNAIL;
          $return['thumbnailUri'] = $thumbnails[3]['url'];
          $return['thumbnailWidth'] = $thumbnails[3]['width'];
          $return['thumbnailHeight'] = $thumbnails[3]['height'];
        }
        break;
      case Media_Item_Video::SUBTYPE_DAILYMOTION:
        $return['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_DAILYMOTION_THUMBNAIL;
        $return['thumbnailUri'] = 'http://www.dailymotion.com/thumbnail/320x240/video/' . $matches[4][0];
        $return['thumbnailWidth'] = 320;
        $return['thumbnailHeight'] = 240;
        break;
      case Media_Item_Video::SUBTYPE_VIMEO:
        $uri = 'http://vimeo.com/api/v2/video/' . $matches[4][0] . '.php';
        $client = new Zend_Http_Client($uri);
        $response = $client->request();
        $vimeo = unserialize($response->getBody());
        $return['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_VIMEO_THUMBNAIL;
        $return['thumbnailUri'] = $vimeo[0]['thumbnail_medium'];
        $return['thumbnailWidth'] = 200;
        $return['thumbnailHeight'] = floor($vimeo[0]['width'] * 0.234375); // 150 / 200 * ( 200 / 640 )
        break;
      default:
        throw new Lib_Exception_Media("Unsupported video provider: '{$matches[1][1]}'");
        break;
    }

    $return['mediaSubType'] = $matches[1][0];
    $return['width'] = $matches[2][0];
    $return['height'] = $matches[3][0];
    $return['uri'] = $matches[4][0];
    $return['size'] = 0;

    $return = $this->_writeLocalVideoThumbnail($return);

    return $return;
  }

  protected function _writeLocalVideoThumbnail($params)
  {
    if (APPLICATION_ENV == 'test') {
      $params['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_JPG;
      $params['thumbnailWidth'] = 160;
      $params['thumbnailHeight'] = 120;
      $params['thumbnailUri'] = 'fakeThumb';
      return $params;
    }
    $file = file_get_contents($params['thumbnailUri']);
    if (empty($file)) {
      return $params;
    }
    $destination = APP_MEDIA_THUMBNAILS_DIR . '/' . md5(uniqid(rand())) . '.jpg';
    file_put_contents($destination, $file);
    $thumbnail = new File_Photo($destination);
    $thumbnail->resize(200, 150);

    $params['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_JPG;
    $params['thumbnailWidth'] = $thumbnail->getWidth();
    $params['thumbnailHeight'] = $thumbnail->getHeight();
    $params['thumbnailUri'] = $destination;

    return $params;
  }

  /**
   * Proceeds to save the photo file, resize it if necessary,
   * and create the thumbnail
   *
   * @param Media_Item_Photo_Row $photo
   * @param Media_Item_Photo_Form $form
   * @param string $dir
   * @param int $id
   * @return array
   */
  protected function _savePhotoFiles(Media_Item_Photo_Row $photo, Media_Item_Photo_Form $form, $dir, $id)
  {
    $photoElement = $form->media;
    if (!$photoElement->isRequired() && !isset($_FILES['media'])) {
      return array();
    }

    $targetName = $this->_getMediaFileName($form->title->getValue(), $id);
    $destination = $dir . DIRECTORY_SEPARATOR . $targetName;

    $photoElement->addFilter('Rename', array('target' => $destination));
    if (!$photoElement->receive()) {
      throw new Lib_Exception("An error occured while receiving photo file '{$photoElement->getValue()}'");
    }

    try {
      $photoFile = new File_Photo($destination, true);
      $photoFile->renameAfterSubType();
      $photoFile->limitDimensions();
      $thumbnail = $photoFile->createThumbnail(APP_MEDIA_THUMBNAILS_DIR, GLOBAL_DEFAULT_IMG_THUMB_WIDTH, GLOBAL_DEFAULT_IMG_THUMB_HEIGHT);
    } catch (Exception $e) {
      $this->_cleanUpPhotoFiles($photo, $destination);
      throw $e;
    }

    return array(
      'path' => GLOBAL_UPLOAD_DEST,
      'uri' => $photoFile->getName(),
      'mediaSubType' => $photoFile->getSubType(),
      'width' => $photoFile->getWidth(),
      'height' => $photoFile->getHeight(),
      'size' => $photoFile->getFileSize(),
      'thumbnailUri' => $thumbnail->getName(),
      'thumbnailWidth' => $thumbnail->getWidth(),
      'thumbnailHeight' => $thumbnail->getHeight(),
      'thumbnailSubType' => $thumbnail->getSubType(),
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
      // Erase any rider tags on this media
      $mediaRidersTable = new Media_Item_Riders();
      $mediaRidersTable->delete(array('id' => $media->id));
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