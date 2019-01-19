<?php

class Media_Item extends Data
{
  const TYPE_PHOTO = 'photo';
  const TYPE_VIDEO = 'video';

  const SMALL = 'small';
  const MEDIUM = 'medium';
  const LARGE = 'large';

  public static $itemTypes = array(
    0 => self::TYPE_PHOTO,
    1 => self::TYPE_VIDEO,
  );

  public static $allowedThumbnailSubTypes = array(
    Media_Item_Photo::SUBTYPE_JPG,
    Media_Item_Photo::SUBTYPE_PNG,
    Media_Item_Photo::SUBTYPE_GIF,
    Media_Item_Photo::SUBTYPE_WEBP,
  );

  public static $thumbSizes = array(
    self::SMALL  => array('width' => 240, 'height' => 135, 'suffix' => '_ts'),
    self::MEDIUM => array('width' => 400, 'height' => 225, 'suffix' => '_tm'),
    self::LARGE  => array('width' => 640, 'height' => 360, 'suffix' => '_tl'),
  );

  protected $_itemType = 'media_Item';

  protected $_name = Constants_TableNames::MEDIA;

  /**
   * Name of the class representing a row
   *
   * @var string
   */
  protected $_rowClass = 'Media_Item_Row';

  /**
   * @var array
   */
  protected $_referenceMap = array(
    'Album' => array(
      'columns' => 'albumId',
      'refTableClass' => 'Media_Album',
      'refColumns' => 'id'
    ),
    'Spot' => array(
      'columns' => 'spot',
      'refTableClass' => 'Spot',
      'refColumns' => 'id'
    ),
    'Trick' => array(
      'columns' => 'trick',
      'refTableClass' => 'Trick',
      'refColumns' => 'id'
    ),
    'LastEditor' => array(
      'columns' => 'lastEditor',
      'refTableClass' => 'User',
      'refColumns' => User::COLUMN_USERID
    ),
    'Location' => array(
      'columns' => 'location',
      'refTableClass' => 'Location',
      'refColumns' => 'id'
    ),
    'Submitter' => array(
      'columns' => 'submitter',
      'refTableClass' => 'User',
      'refColumns' => User::COLUMN_USERID
    ),
    'Author' => array(
      'columns' => 'author',
      'refTableClass' => 'User',
      'refColumns' => User::COLUMN_USERID
    )
  );
}