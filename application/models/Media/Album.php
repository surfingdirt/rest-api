<?php

/**
 * A media item is a member of a Media_Album.
 */
class Media_Album extends Data
{
  const TYPE_SIMPLE = 'simple';
  const TYPE_AGGREGATE = 'aggregate';

  const CONTRIBUTIONS_PUBLIC = 'public';
  const CONTRIBUTIONS_PRIVATE = 'private';

  const VISIBILITY_PRIVATE = 'private';
  const VISIBILITY_UNLISTED = 'unlisted';
  const VISIBILITY_VISIBLE = 'visible';

  const CREATION_STATIC = 'static';
  const CREATION_AUTOMATIC = 'automatic';
  const CREATION_USER = 'user';

  const ALBUM_PHOTO = 'photo';
  const ALBUM_VIDEO = 'video';

  public static $albumTypes = array(
    0 => self::TYPE_SIMPLE,
    1 => self::TYPE_AGGREGATE,
  );

  public static $albumContributions = array(
    0 => self::CONTRIBUTIONS_PUBLIC,
    1 => self::CONTRIBUTIONS_PRIVATE,
  );

  public static $albumCreations = array(
    0 => self::CREATION_STATIC,
    1 => self::CREATION_AUTOMATIC,
    2 => self::CREATION_USER,
  );

  public static $mainAlbums = array(
    0 => self::ALBUM_PHOTO,
    1 => self::ALBUM_VIDEO,
  );

  public static $mainAlbumIds = array('a3833b1c-1db0-4a93-9efc-b6659400ce9f');

  protected $_itemType = 'mediaAlbum';

  protected $_name = Constants_TableNames::ALBUM;

  /**
   * Name of the class representing a row
   *
   * @var string
   */
  protected $_rowClass = 'Media_Album_Row';

  public static function getPublicAlbumIds()
  {
    $array = array(
      Media_Album_PhotoMain::ID,
      Media_Album_VideoMain::ID,
    );
    return $array;
  }

  public static function createAggregateAlbumFor(Data_Row $data)
  {
    $albumTable = new Media_Album_Aggregate();
    $album = $albumTable->fetchNew();

    $title = Globals::getTranslate()->translate('albumFor');
    $title = sprintf($title, $data->getTitle());
    $storedTitle = Lib_Translate::encodeField([
      ['locale' => DEFAULT_LOCALE, 'text' => $title],
    ]);

    $album->submitter = $data->submitter;
    $album->title = $storedTitle;
    $album->description = $storedTitle;
    $album->date = $data->date;

    $album->albumType = self::TYPE_AGGREGATE;
    $album->albumCreation = self::CREATION_AUTOMATIC;
    $album->albumContributions = self::CONTRIBUTIONS_PUBLIC;

    if ($data instanceof Spot_Row) {
      $album->spot = $data->id;
    }
    if (!$album->save()) {
      return false;
    }
    $album->clearCache();

    $aggregationTable = new Media_Album_Aggregation();
    $aggregation = $aggregationTable->fetchNew();
    $aggregation->keyValue = $data->id;
    $aggregation->albumId = $album->id;
    $aggregation->keyName = $data->getItemType();
    $id = $aggregation->save();
    $status = ($id) ? true : false;
    return $status;
  }

  public static function createAggregateAlbumForUser(User_Row $user)
  {
    $albumTable = new Media_Album_Aggregate();
    $album = $albumTable->fetchNew();

    $title = Globals::getTranslate()->translate('albumFor');
    $title = sprintf($title, $user->getTitle());
    $storedTitle = Lib_Translate::encodeField([
      ['locale' => DEFAULT_LOCALE, 'text' => $title],
    ]);

    $album->id = Utils::uuidV4();
    $album->submitter = $user->getId();
    $album->title = $storedTitle;
    $album->description = $storedTitle;
    $album->date = $user->date;

    $album->albumType = self::TYPE_AGGREGATE;
    $album->albumCreation = self::CREATION_AUTOMATIC;
    $album->albumContributions = self::CONTRIBUTIONS_PUBLIC;

    if (!$album->save()) {
      return false;
    }

    $aggregationTable = new Media_Album_Aggregation();
    $aggregation = $aggregationTable->fetchNew();
    $aggregation->keyValue = $user->{User::COLUMN_USERID};
    $aggregation->albumId = $album->id;
    $aggregation->keyName = Constants_DataTypes::USER;
    $id = $aggregation->save();
    $status = ($id) ? true : false;
    return $status;
  }

  public static function deleteAggregateAlbumFor(Data_Row $data)
  {
    $itemType = $data->getItemType();

    $aggregationTable = new Media_Album_Aggregation();
    $where = "keyName = '$itemType' AND keyValue = '$data->id'";
    $aggregation = $aggregationTable->fetchRow($where);
    if (empty($aggregation)) {
      // No album to be deleted.
      return;
    }
    $aggregationTable->delete("id = {$aggregation['id']}");

    $albumTable = new Media_Album();
    $albumTable->delete("id = {$aggregation['albumId']}");
  }

  public static function createUserAlbum(User_Row $user)
  {
    $albumTable = new Media_Album_Aggregate();
    $album = $albumTable->fetchNew();
    $album->submitter = $user->getId();
    $album->date = $user->date;
    $album->title = Lib_Translate::encodeField([
      ['locale' => DEFAULT_LOCALE, 'text' => 'album for ' . $user->getTitle()],
    ]);
    $album->description = Lib_Translate::encodeField([
      ['locale' => DEFAULT_LOCALE, 'text' => 'album for ' . $user->getTitle()],
    ]);
    $album->userId = $user->{User::COLUMN_USERID};
    $album->albumType = self::TYPE_AGGREGATE;
    $album->albumCreation = self::CREATION_AUTOMATIC;
    $album->albumContributions = self::CONTRIBUTIONS_PUBLIC;

    if (!$album->save()) {
      return false;
    }

    $aggregationTable = new Media_Album_Aggregation();
    $aggregation = $aggregationTable->fetchNew();
    $aggregation->keyValue = $user->{User::COLUMN_USERID};
    $aggregation->albumId = $album->id;
    $aggregation->keyName = User::ALBUM_KEYNAME;
    $status = $aggregation->save();

    return $status;
  }

  public static function getCacheId($albumId)
  {
    $cacheId = 'mediaalbum' . $albumId;
    return str_replace('-', '', $cacheId);
  }

  /**
   * Create a simple album for a data object
   *
   * @param Data_Row $data
   * @param string|null $access
   * @return boolean
   */
  public static function createSimpleAlbumFor(Data_Row $data, $access = null)
  {
    if (empty($access)) {
      $access = Media_Album::CONTRIBUTIONS_PUBLIC;
    }
    if ($access != Media_Album::CONTRIBUTIONS_PUBLIC) {
      $access = Media_Album::CONTRIBUTIONS_PRIVATE;
    }

    $albumTable = new Media_Album_Simple();
    $albumItemTable = new Media_Album_Item();

    $album = $albumTable->fetchNew();
    $album->submitter = $data->submitter;
    $album->date = $data->date;
    $album->title = Lib_Translate::encodeField([
      ['locale' => DEFAULT_LOCALE, 'text' => 'album for ' . $user->getTitle()],
    ]);
    $album->description = Lib_Translate::encodeField([
      ['locale' => DEFAULT_LOCALE, 'text' => 'album for ' . $user->getTitle()],
    ]);
    $album->albumCreation = self::CREATION_AUTOMATIC;
    $album->albumContributions = $access;

    if (!$album->save()) {
      return false;
    }

    $albumItem = $albumItemTable->fetchNew();
    $albumItem->albumId = $album->id;
    $albumItem->itemId = $data->id;
    $albumItem->itemType = $data->getItemType();
    $id = $albumItem->save();
    $status = ($id) ? true : false;
    return $status;
  }
}