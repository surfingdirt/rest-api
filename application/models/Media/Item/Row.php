<?php

class Media_Item_Row extends Data_Row implements Data_Row_AlbumInterface,
  Data_Row_MediaItemInterface
//  Data_Row_SpotInterface,
//  Data_Row_TrickInterface,
//  Data_Row_LocationInterface,
//  Data_Row_DocumentInterface,
//  Data_Row_DptInterface
{
  /**
   * Location of the spot
   *
   * @var Location_Row
   */
  protected $_location;

  protected $_isTitleTranslated = false;

  protected $_isDescriptionTranslated = false;


  public function getTitle()
  {
      /* Media item title may be empty */
      $title = $this->{$this->_titleColumn};
      return $title;
    }

    protected function _postDelete()
    {
      parent::_postDelete();

      $table = new Media_Item_Users();
      $where = $table->getAdapter()->quoteInto("mediaId = ?", $this->id);
      $table->delete($where);
    }

    /**
     * Returns a list of all cache id's that must be emptied
     * when saving/deleting this object.
     */
  protected function _getCacheIdsForClear()
  {
    $return = parent::_getCacheIdsForClear();
    $return[] = $this->getAlbum()->getItemsCacheId();
    $return[] = $this->_getUsersInMediaCacheId();
    $return[] = $this->getParentRowCacheId('User', 'Author');

    $trick = $this->getTrick();
    if ($trick instanceof Trick_Row) {
      $return[] = $this->getParentRowCacheId($trick->getItemType());

      if ($trickAlbum = $trick->getAlbum()) {
        $return[] = $trickAlbum->getItemsCacheId();
      }
    }

    $spot = $this->getSpot();
    if ($spot instanceof Spot_Row) {
      $return[] = $this->getParentRowCacheId($spot->getItemType());

      if ($spotAlbum = $spot->getAlbum()) {
        $return[] = $spotAlbum->getItemsCacheId();
      }
    }

    $users = $this->getUsersInMedia();
    foreach ($users['byId'] as $user) {
      if ($user && ($album = $user->getAlbum())) {
        $return[] = $album->getItemsCacheId();
      }
    }

    return $return;
  }

  /**
   * Returns the type of media represented by the current object
   * (photo, video, etc.)
   * @return string
   */
  public function getMediaType()
  {
    return $this->mediaType;
  }

  /**
   * Returns the sub-type of media represented by the current object
   * (jpg, gif, youtube, vimeo, flv, etc.)
   *
   * @return string
   * @throws Lib_Exception_Media
   */
  public function getThumbnailSubType()
  {
    if (!in_array($this->thumbnailSubType, Media_Item::$allowedThumbnailSubTypes)) {
      throw new Lib_Exception_Media("Bad subtype: '$this->mediaSubType' for media '$this->id'");
    }
    return $this->thumbnailSubType;
  }

  /**
   * Returns the URI used to embed element in a view
   * @return string
   */
  public function getURI()
  {
    return $this->uri;
  }

  /**
   * Returns the width (in pixels) of the current object
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Returns the height (in pixels) of the current object
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * Returns the full size in bytes of the current object
   *
   * @return unknown
   */
  public function getSize()
  {
    return $this->size;
  }

  /**
   * Returns the size of the current object in a neat way,
   * translated in the right unit.
   *
   * @return string
   */
  public function getDisplaySize()
  {
    $translator = Globals::getTranslate();

    $currentNumber = $this->size;

    $sizeArray = array('B', 'KB', 'MB', 'GB', 'TB');
    $index = 0;

    while ($currentNumber > 1024) {
      $currentNumber = round($currentNumber / 1024, 1);
      $index++;
    }

    $string = $currentNumber . $translator->translate($sizeArray[$index]);
    return $string;
  }

  public function getMediaSubType()
  {
    throw new Lib_Exception("Media_Item_Row must not be asked for mediaSubType");
  }

  /**
   * Returns the URI used to embed the thumbnail element in a view
   * @return string
   */
  public function getThumbnailURI($absolute = true)
  {
    if ($absolute) {
      $url = APP_URL . '/' . APP_MEDIA_THUMBNAILS_DIR . '/' . $this->thumbnailUri;
    } else {
      $url = APP_MEDIA_THUMBNAILS_DIR . '/' . $this->thumbnailUri;
    }
    return $url;
  }

  /**
   * Returns the width (in pixels) of the thumbnail
   *
   * @return integer
   */
  public function getThumbnailWidth()
  {
    return $this->thumbnailWidth;
  }

  /**
   * Returns the height (in pixels) of the thumbnail
   *
   * @return integer
   */
  public function getThumbnailHeight()
  {
    return $this->thumbnailHeight;
  }

  /**
   * Return the spot for current item
   *
   * @return Spot_Row
   */
  public function getSpot()
  {
    if (empty($this->spot)) {
      return null;
    }
    $spot = $this->findParentRow('Spot');
    return $spot;
  }

  public function hasSpot()
  {
    return (!empty($this->spot));
  }

  public function getSpotName()
  {
    if (empty($this->spot)) {
      return null;
    }

    $spotInfo = $this->getSpotNameAndLink();
    return $spotInfo['name'];
  }

  public function getSpotNameAndLink()
  {
    $return = array(
      'name' => null,
      'link' => null,
    );

    if (empty($this->spot)) {
      return $return;
    }

    if (strpos($this->spot, NOREALDATA_MARK) !== false) {
      // spotname is stored directly in DB
      $return['name'] = str_replace(NOREALDATA_MARK, '', $this->spot);
      return $return;
    }

    // spotId is stored in DB
    $spot = $this->findParentRow('Spot');
    if (!empty($spot)) {
      $return['name'] = $spot->getTitle();
      $return['link'] = $spot->getLink();
    }
    return $return;
  }

  /**
   * Return the trick for current item
   *
   * @return Trick_Row
   */
  public function getTrick()
  {
    if (empty($this->trick)) {
      return null;
    }

    if (strpos($this->trick, NOREALDATA_MARK) !== false) {
      // trickname is stored directly in DB
      $return['name'] = str_replace(NOREALDATA_MARK, '', $this->trick);
      return $return;
    }

    $trick = $this->findParentRow('Trick');
    return $trick;
  }

  public function hasTrick()
  {
    return (!empty($this->trick));
  }

  public function getTrickName()
  {
    if (empty($this->trick)) {
      return null;
    }

    $trickInfo = $this->getTrickNameAndLink();
    return $trickInfo['name'];
  }

  public function getTrickNameAndLink()
  {
    $return = array(
      'name' => null,
      'link' => null,
    );

    if (empty($this->trick)) {
      return $return;
    }

    if (strpos($this->trick, NOREALDATA_MARK) !== false) {
      // trickname is stored directly in DB
      $return['name'] = str_replace(NOREALDATA_MARK, '', $this->trick);
      return $return;
    }

    // trickId is stored in DB
    $trick = $this->findParentRow('Trick');
    if (!empty($trick)) {
      $return['name'] = $trick->getTitle();
      $return['link'] = $trick->getLink();
    }
    return $return;
  }

  public function hasLocation()
  {
    $location = $this->getLocation();
    $return = (!empty($location));
    return $return;
  }

  /**
   * Return the department for current item
   *
   * @return Location_Row
   */
  public function getLocation()
  {
    if (empty($this->id)) {
      // This was never saved: can't have a location!
      return null;
    }

    $cacheId = $this->_getLocationCacheId();
    $cache = $this->getCache();

    /*
     * How can we differentiate 'no location' and 'no cache'?
     * We save 0 in cache if no location.
     */
    $noLocationMarker = 0;

    $table = new Location();
    $location = $cache->load($cacheId);
    if ($location instanceof Location_Row) {
      $location->setTable($table);
      return $location;
    } elseif ($location === $noLocationMarker) {
      return null;
    }

    $where = "itemType = '" . $this->getItemType() . "' AND itemId = " . $this->getId();
    $location = $table->fetchRow($where);
    if ($location === null) {
      $cache->save($noLocationMarker, $cacheId);
      return null;
    }

    $cache->save($location, $cacheId);
    return $location;
  }

  public function setLocation(Location_Row $location)
  {
    $this->_location = $location;
  }

  /**
   * Determine whether an author was designated for this article
   *
   * @return boolean
   */
  public function hasAuthor()
  {
    $return = !empty($this->author);
    return $return;
  }

  /**
   * Return the author of current item
   *
   */
  public function getAuthor()
  {
    if (strpos($this->author, NOREALDATA_MARK) !== false) {
      // username is stored directly in DB
      $return['name'] = ucfirst(str_replace(NOREALDATA_MARK, '', $this->author));
      return $return;
    }

    $author = $this->findParentRow('User', 'Author');
    return $author;
  }

  /**
   * Return the name of the author of current item
   *
   */
  public function getAuthorName()
  {
    if (empty($this->author)) {
      return null;
    }

    $authorInfo = $this->getAuthorNameAndLink();
    return ucfirst($authorInfo['name']);
  }

  /**
   * Return an array of parameters needed to build a link to the author of
   * the current item.
   * If the author is not registered on the website, only their name will be returned.
   *
   * @return array
   */
  public function getAuthorNameAndLink()
  {
    $return = array(
      'name' => null,
      'link' => null,
    );

    if (empty($this->author)) {
      return $return;
    }

    if (strpos($this->author, NOREALDATA_MARK) !== false) {
      // username is stored directly in DB
      $return['name'] = ucfirst(str_replace(NOREALDATA_MARK, '', $this->author));
      return $return;
    }

    // userId is stored in DB
    $author = $this->findParentRow('User', 'Author');
    if (!empty($author)) {
      $return['name'] = ucfirst($author->{User::COLUMN_USERNAME});
      $return['link'] = $author->getLink();
    }
    return $return;
  }

  /**
   * Returns the parent album
   *
   * @return Media_Album_Row
   */
  public function getAlbum($skipCache = false)
  {
    $cache = $this->getCache();
    $cacheId = $this->_getAlbumCacheId();
    $albums = $cache->load($cacheId);
    if (!$albums || $skipCache) {
      $album = Media_Album_Factory::buildAlbumById($this->albumId);
      $rowset = new Zend_Db_Table_Rowset(array(
        'data' => array($album->toArray()),
        'rowClass' => get_class($album),
        'tableClass' => $album->getTableClass()
      ));
      $this->getTable()->saveDataInCache($cache, $rowset, $cacheId);
    } else {
      $albums->rewind();
      $album = $albums->current();
    }

    return $album;
  }

  protected function _getAlbumItemsCacheId()
  {
    $cacheId = $this->getAlbum()->getItemsCacheId();
    return $cacheId;
  }

  protected function _getAlbumCacheId()
  {
    $cacheId = Media_Album::getCacheId($this->albumId);
    return $cacheId;
  }

  /**
   * Returns the name of the view script to use when rendering
   *
   * @return string
   */
  public function getViewScript()
  {
    switch ($this->mediaType) {
      default:
        $view = 'media/displaymedia.phtml';
        break;
    }
    return $view;
  }

  public function getNextItemInAlbum()
  {
    $album = $this->getAlbum();
    $nextItem = $album->getNextItem();
    return $nextItem;
  }

  public function getPreviousItemInAlbum()
  {
    $album = $this->getAlbum();
    $previousItem = $album->getPreviousItem();
    return $previousItem;
  }

  /**
   * Returns an array containing:
   *  - a list of users tagged in this media.
   *  - a list of other people not members, but in this media.
   *
   * @return array
   */
  public function getUsersInMedia()
  {
    $users = false;
    $userIdList = array();
    $userNameList = array();
    $userList = array();

    $table = new Media_Item_Users();
    if (ALLOW_CACHE) {
      $cacheId = $this->_getUsersInMediaCacheId();
      $cache = $this->getCache();
      $users = $cache->load($cacheId);
    }
    if ($users === false) {
      $where = $table->getAdapter()->quoteInto("mediaId = ?", $this->id);
      $rowset = $table->fetchAll($where);
      foreach ($rowset as $row) {
        if ($row['userId']) {
          $userIdList[] = $row['userId'];
        } else {
          if (empty($row['userName'])) {
            continue;
          }
          $userNameList[] = $row['userName'];
        }
      }

      $table = new Api_User();
      if ($userIdList) {
        $where = User::COLUMN_USERID . " IN ('" . implode("', '", $userIdList) . "')";
        $userList = $table->fetchAll($where);
      }
      $users = array(
        'byId' => $userList,
        'byName' => $userNameList
      );

      // No cache entry, let's create one
      if (ALLOW_CACHE) {
        $cache->save($users, $cacheId);
      }
    }

    return $users;
  }

  public function getUserIdsInMedia()
  {
    $list = $this->getUsersInMedia();
    $userIds = array();
    foreach ($list['byId'] as $user) {
      $userIds[] = $user->getId();
    }
    return $userIds;
  }

  protected function _getUsersInMediaCacheId()
  {
    return 'usersInMedia' . $this->_getIdForCache($this->getId());
  }

  public function getUserNamesInMedia()
  {
    $users = $this->getUsersInMedia();
    $list = array();
    $list['byId'] = array();
    foreach ($users['byId'] as $user) {
      $list['byId'][] = $user->{User::COLUMN_USERNAME};
    }

    $list['byId'] = implode(', ', $list['byId']);
    $list['byName'] = implode(', ', $users['byName']);
    if (empty($users['byId'])) {
      return $list['byName'];
    }
    if (empty($users['byName'])) {
      return $list['byId'];
    }

    $return = implode(', ', $list);
    return $return;
  }

  /**
   * Returns the url that the user will be redirected to
   * upon deletion of this object
   *
   * @return string
   */
  protected function _getDeleteRedirectUrl($params, User_Row $user)
  {
    $url = $this->getAlbum($user)->getLink();
    return $url;
  }

  /**
   * No folders for media items
   */
  public function getFolderPath()
  {
  }

  /**
   * Returns a string representing the content.
   * Used for search exceprt generation.
   */
  public function getFlatContent()
  {
    $return = ucfirst($this->getTitle());
    $return .= ' ' . $this->getDescription();
    $return .= ' ' . implode(' ', $this->getTags());
    return $return;
  }

  public function getCityDptAndCountry()
  {
    $city = $dptRow = $countryRow = null;

    if ($this->hasLocation()) {
      $location = $this->getLocation();
      $dptRow = $location->getDpt();
      $countryRow = $location->getCountry();
      $city = ucfirst($location->city);
    } elseif (!empty($this->dpt)) {
      $dptTable = new Dpt();
      $dptRow = $dptTable->find($this->dpt)->current();
      if (empty($dptRow)) {
        $msg = "Media " . $this->getId() . " has no location, but has a dpt: '{$this->dpt}'.";
        $msg .= " Dpt '{$this->dpt}' could not be found in database.";
        Globals::getLogger()->locations($msg);
      }
    }

    return array($city, $dptRow, $countryRow);
  }

  public function getDpt()
  {
    list($city, $dptRow, $countryRow) = $this->getCityDptAndCountry();
    return $dptRow;
  }
}