<?php

class User_Row extends Zend_Db_Table_Row implements Zend_Acl_Role_Interface,
  Data_Row_DptInterface
{
  protected $_titleColumn = 'username';

  protected $_route = 'userprofile';

  protected $_notificationTable = 'User_Notification';

  protected function _doInsert()
  {
    parent::_doInsert();

    //$this->_createBlog();

    // Album creation
    Media_Album::createAggregateAlbumForUser($this);

    // Notifications creation
    $table = new $this->_notificationTable();
    $notificationLocations = $this->getDefaultNotifications();
    foreach ($notificationLocations as $medium => $notificationsArray) {

      foreach ($notificationsArray as $itemType => $notify) {
        $notificationRow = $table->fetchNew();

        $notificationRow->userId = $this->{User::COLUMN_USERID};
        $notificationRow->medium = $medium;
        $notificationRow->itemType = $itemType;
        $notificationRow->notify = $notify;

        $notificationRow->save();
      }
    }
  }

  public function isValidUser() {
    switch ($this->status) {
      case User::STATUS_GUEST:
      case User::STATUS_BANNED:
      case User::STATUS_PENDING:
        return false;
        break;
      case User::STATUS_MEMBER:
      case User::STATUS_WRITER:
      case User::STATUS_EDITOR:
      case User::STATUS_ADMIN:
        return true;
        break;
      default:
        throw new Api_Exception("Unexpected status for user '" . $this->getId() . "': '" . $this->status . "'");
        break;
    }
  }

  public function isReadableBy(User_Row $user, Lib_Acl $acl)
  {
    if (in_array($this->status, array(User::STATUS_BANNED, User::STATUS_PENDING, User::STATUS_GUEST))) {
      return in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR));
    }

    return true;
  }


  protected function _doUpdate()
  {
    $return = parent::_doUpdate();
    $this->clearCache();
    return $return;
  }

  public function getCache()
  {
    $cache = $this->getTable()->getCache();
    return $cache;
  }

  public function clearCache()
  {
    $cache = $this->getCache();
    $cacheIds = array(
      $this->_getNotificationsCacheId(),
      $this->_getBlogsCacheId(),
      User::VALID_USER_LIST_CACHE_ID,
      'user' . $this->_getIdForCacheId(),
      $this->_getLocationCacheId()
    );

    foreach ($cacheIds as $cacheId) {
      $cache->remove($cacheId);
    }
  }

  public function getId()
  {
    return $this->{User::COLUMN_USERID};
  }

  protected function _getIdForCacheId()
  {
    return str_replace('-', '', $this->getId());
  }

  public function getItemType()
  {
    return $this->_table->getItemType();
  }

  public function getLink()
  {
    $params = array(
      'id' => $this->{User::COLUMN_USERID},
      'name' => Utils::cleanStringForUrl($this->getTitle()),
    );
    $link = Globals::getRouter()->assemble($params, $this->_route, true);
    return $link;
  }

  public function hasLocation()
  {
    $location = $this->getLocation();
    $return = (!empty($location));
    return $return;
  }

  /**
   * Return the location for current item
   *
   * @return Location_Row
   */
  public function getLocation()
  {
    if (empty($this->{User::COLUMN_USERID})) {
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
    $where = $table->getAdapter()->quoteInto('itemType = ?', $this->getItemType());
    $where .= $table->getAdapter()->quoteInto(' AND itemId = ?', $this->getId());
    $location = $table->fetchRow($where);
    if ($location === null) {
      $cache->save($noLocationMarker, $cacheId);
      return null;
    }

    $cache->save($location, $cacheId);
    return $location;
  }

  protected function _getLocationCacheId()
  {
    $cacheId = 'locationFor_' . $this->getItemType() . $this->_getIdForCacheId();
    return $cacheId;
  }

  /**
   * Return an array of parameters needed to build a link to
   * this user
   *
   * @return array
   */
  public function getNameAndLink()
  {
    $return['name'] = $this->{User::COLUMN_USERNAME};
    $return['link'] = $this->getLink();

    return $return;
  }

  /**
   * Return title of the current object
   *
   * @return string
   */
  public function getTitle()
  {
    $titleColumn = $this->_titleColumn;
    $title = $this->$titleColumn;
    return ucfirst($title);
  }

  /**
   * Defined by Zend_Acl_Role_Interface
   * Returns the string identifier of the Role
   *
   * @return string
   */
  public function getRoleId()
  {
    return $this->{User::COLUMN_STATUS};
  }

  /**
   * Indicates whether this user is an admin or not
   * @return boolean
   */
  public function isAdmin()
  {
    $return = $this->getRoleId() == User::STATUS_ADMIN;
    return $return;
  }

  public function isEditor()
  {
    $return = $this->getRoleId() == User::STATUS_EDITOR;
    return $return;
  }

  /**
   * Returns the role of a user owning an object
   *
   * @return Zend_Acl_Role
   */
  public function getOwnerRole()
  {
    if (!$this->isLoggedIn()) {
      return new Zend_Acl_Role(User::STATUS_GUEST);
    }

    $role = new Zend_Acl_Role(Lib_Acl::OWNER_ROLE . '_' . $this->{User::COLUMN_USERID});
    return $role;
  }

  /**
   * Returns this user's profile album
   *
   * @return Media_Album
   * @throws Lib_Exception_User
   */
  public function getAlbum()
  {
    $cache = $this->getCache();
    $cacheId = User::getUserAlbumCacheId($this->getId());
    $album = null;

    if (ALLOW_CACHE) {
      $album = $cache->load($cacheId);
    }
    if (!$album) {
      $album = Media_Album_Factory::buildAggregateUserAlbum($this);
    }
    if (ALLOW_CACHE) {
      $cache->save($album, $cacheId);
    }

    return $album;
  }

  public function isLoggedIn()
  {
    if (!in_array($this->status, array(User::STATUS_BANNED, User::STATUS_GUEST, User::STATUS_PENDING))) {
      return true;
    }
    return false;
  }

  public function canLogin()
  {
    if (in_array($this->status, array(User::STATUS_GUEST))) {
      return true;
    }
    return false;
  }

  /**
   * Returns the path to the upload directory for this user
   * @return string
   */
  public function getUploadFolder()
  {
    list($parentDir, $dir) = $this->_getUploadFolderParts();
    $folder = new Folder($dir . '/');
    return $folder;
  }

  protected function _getUploadFolderParts()
  {
    $id = $this->{User::COLUMN_USERID};
    $parentDir = floor($id / GLOBAL_USER_DIRS_PER_DIR);
    $dir = CURRENT_DIR . '/' . CONTENT_DIRECTORY . $parentDir . '/' . $id;
    return array(
      $parentDir,
      $dir,
    );
  }

  /**
   * Creates user content folder after insert
   *
   */
  protected function _postInsert()
  {
    //$this->createUploadFolder();
  }

  public function createUploadFolder()
  {
    list($parentDir, $dir) = $this->_getUploadFolderParts();
    if (is_dir($dir)) {
      return;
    }

    if (!is_dir(CURRENT_DIR . '/' . CONTENT_DIRECTORY . $parentDir)) {
      mkdir(CURRENT_DIR . '/' . CONTENT_DIRECTORY . $parentDir);
    }

    mkdir($dir);
  }

  protected function _postDelete()
  {
    parent::_postDelete();
    // Album deletion
    Media_Album::deleteAggregateAlbumFor($this);
    $this->deleteUploadFolder();
  }

  public function deleteUploadFolder()
  {
    list($parentDir, $dir) = $this->_getUploadFolderParts();
    if (!is_dir($dir)) {
      return;
    }

    rmdir($dir);
  }

  /**
   * Returns an array of itemTypes to be notified for this user
   * and the given medium
   *
   * @param string $medium
   *
   * @return User_Notification_Row|null
   *
   */
  public function getApplicableNotifications($medium)
  {
    $return = array();
    $notifications = $this->getNotifications($medium);
    $notifications = $this->addDefaultNotifications($notifications, $medium);
    foreach ($notifications as $notification) {
      if ($notification->notify == User_Notification::NOTIFY) {
        $return[] = $notification->itemType;
      }
    }

    return $return;
  }

  /**
   * Returns all notifications for the given medium
   *
   * @param string $medium
   * @return array
   */
  public function getNotifications($medium)
  {
    if (!in_array($medium, User_Notification::$mediums)) {
      throw new Lib_Exception("Unknown notification medium '$medium'");
    }

    $cacheId = $this->_getNotificationsCacheId();
    $cache = $this->getCache();
    $notifications = $cache->load($cacheId);
    if (!$notifications) {
      $notifications = $this->findDependentRowset($this->_notificationTable);
      $this->getTable()->saveDataInCache($cache, $notifications, $cacheId);
    } else {
      $notifications->setTable(new $this->_notificationTable());
    }

    $return = array();
    foreach ($notifications as $notification) {
      if ($notification->medium == $medium) {
        $return[$notification->itemType] = $notification;
      }
    }

    return $return;
  }

  protected function _getNotificationsCacheId()
  {
    $return = 'notificationsForUser' . $this->_getIdForCacheId($this->getId());
    return $return;
  }

  /**
   * Adds notification information in the database if it does not
   * exist, using default values.
   */
  public function addDefaultNotifications($notifications, $medium)
  {
    $added = false;

    $table = new $this->_notificationTable();
    $availableNotifications = User_Notification::$available;

    foreach ($availableNotifications as $itemType => $notify) {
      if (!isset($notifications[$itemType])) {
        $currentNotification = $table->fetchNew();
        $currentNotification->userId = $this->{User::COLUMN_USERID};
        $currentNotification->itemType = $itemType;
        $currentNotification->medium = $medium;
        $currentNotification->notify = $notify;
        $currentNotification->save();
        $added = true;
        $notifications[$itemType] = $currentNotification;
      }
    }

    if ($added) {
      $this->clearCache();
    }

    return $notifications;
  }

  /**
   * Returns an array of notifications that must be applied
   * to all new users, for every given 'medium'
   *
   * @return array
   */
  public function getDefaultNotifications()
  {
    $return = array(
      User_Notification::MEDIUM_HOMEPAGE => array(
        Constants_DataTypes::ALBUM => User_Notification::DO_NOT_NOTIFY,
        Constants_DataTypes::COMMENT => User_Notification::NOTIFY,
        Constants_DataTypes::DOSSIER => User_Notification::NOTIFY,
        Constants_DataTypes::EVENT => User_Notification::NOTIFY,
        Constants_DataTypes::MEDIAALBUM => User_Notification::NOTIFY,
        Constants_DataTypes::NEWS => User_Notification::NOTIFY,
        Constants_DataTypes::PHOTO => User_Notification::NOTIFY,
        Constants_DataTypes::FORUMPOST => User_Notification::NOTIFY,
        Constants_DataTypes::PRIVATEMESSAGE => User_Notification::NOTIFY,
        Constants_DataTypes::FORUMTOPIC => User_Notification::NOTIFY,
        Constants_DataTypes::TRICK => User_Notification::NOTIFY,
        Constants_DataTypes::SPOT => User_Notification::NOTIFY,
        Constants_DataTypes::USER => User_Notification::NOTIFY,
        Constants_DataTypes::VIDEO => User_Notification::NOTIFY,
      ),
    );
    return $return;
  }

  public function getNewUnreadPrivateMessages($date)
  {
    $table = new PrivateMessage();
    $results = $table->fetchAll("`read` = 0 AND date > '$date' AND toUser = " . $this->{User::COLUMN_USERID});
    return $results;
  }

  public function getOldUnreadPrivateMessages($date)
  {
    $table = new PrivateMessage();
    $results = $table->fetchAll("`read` = 1 AND date > '$date' AND toUser = " . $this->{User::COLUMN_USERID});
    return $results;
  }

  /**
   * Returns this user's blog
   *
   */
  public function getBlog()
  {
    $blogs = null;

    if (ALLOW_CACHE) {
      $cacheId = $this->_getBlogsCacheId();
      $cache = $this->getCache();
      $blogs = $cache->load($cacheId);
    }
    if (!$blogs) {
      $blogs = $this->findDependentRowset('Blog', 'Submitter');
      if (ALLOW_CACHE) {
        $this->getTable()->saveDataInCache($cache, $blogs, $cacheId);
      }
    } else {
      $blogs->setTable(new Blog());
    }

    if (count($blogs) == 0) {
      throw new Lib_Exception("Could not find a blog for user " . $this->{User::COLUMN_USERID});
    }
    $blog = $blogs[0];
    return $blog;
  }

  public function getCountry()
  {
    $table = new Country();
    $result = $table->find($this->country);
    if (empty($result)) {
      return null;
    }
    $country = $result->current();
    if ($country) {
      return $country->getTitle();
    }

    return null;
  }

  protected function _getBlogsCacheId()
  {
    $return = 'blogsForUser' . $this->_getIdForCacheId($this->getId());
    return $return;
  }

  public function getAvatar($baseUrl = null)
  {
    return $this->avatar;
  }

  public function getHeaderCacheIdPrefix()
  {
    $cacheId = 'headerForUser' . $this->_getIdForCacheId() . '_' . Zend_Registry::get('Zend_Locale');
    return $cacheId;
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
        $msg = "User " . $this->{User::COLUMN_USERID} . " has no location, but has a dpt: '{$this->dpt}'.";
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

  public function getLastPostedItem($itemType)
  {
    $table = new Item();
    $return = $table->getLastPostedItemBy($this, $itemType);
    return $return;
  }

  public function getLastPostedPhotoItem()
  {
    $return = $this->getLastPostedItem(Media_Item::TYPE_PHOTO);
    return $return;
  }

  public function getFacebookInfo($site)
  {
    /**
     * @todo find userId = $this->getId() in facebook users table
     */
  }

  protected function _createBlog()
  {
    $blogTable = new Blog();
    $newBlog = $blogTable->fetchNew();
    $newBlog->date = Utils::date('Y-m-d H:i:s');
    $newBlog->title = "Blog de " . $this->{User::COLUMN_USERNAME};
    $newBlog->description = "Le blog de " . $this->{User::COLUMN_USERNAME};
    $newBlog->id = $this->{User::COLUMN_USERID};
    $newBlog->submitter = $this->{User::COLUMN_USERID};
    $newBlog->status = Data::VALID;
    $newBlog->save();
  }

}