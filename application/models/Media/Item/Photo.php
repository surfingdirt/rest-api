<?php

class Media_Item_Photo extends Media_Item
{
  const SUBTYPE_IMG = 'img';
  const SUBTYPE_JPG = 'jpg';
  const SUBTYPE_PNG = 'png';
  const SUBTYPE_GIF = 'gif';
  const SUBTYPE_WEBP = 'webp';

  const EXT_JPG = 'jpg';
  const EXT_PNG = 'png';
  const EXT_GIF = 'gif';
  const EXT_WEBP = 'webp';

  const SUBTYPE_FLICKR = 'flickr';
  const SUBTYPE_VIMEO_THUMBNAIL = 'vimeo_thumb';
  const SUBTYPE_YOUTUBE_THUMBNAIL = 'youtube_thumb';
  const SUBTYPE_DAILYMOTION_THUMBNAIL = 'dailymotion_thumb';

  const MIME_JPG = 'image/jpg';
  const MIME_JPEG = 'image/jpeg';
  const MIME_PNG = 'image/png';
  const MIME_GIF = 'image/gif';
  const MIME_WEBP = 'image/gif';

  /**
   * Name of the class representing a row
   *
   * @var string
   */
  protected $_rowClass = 'Media_Item_Photo_Row';

  protected $_itemType = 'photo';

  public static $allowedMediaSubTypes = array(
    self::SUBTYPE_IMG,
    self::SUBTYPE_JPG,
    self::SUBTYPE_PNG,
    self::SUBTYPE_GIF,
    self::SUBTYPE_WEBP,
  );

  public static $allowedMimeTypes = array(
    self::MIME_JPG,
    self::MIME_JPEG,
    self::MIME_PNG,
    self::MIME_GIF,
    self::MIME_WEBP,
  );

  public static function getAllowedExtensionsString()
  {
    return 'jpg,jpeg,png,gif';
  }

  public static function getAllowedMimeTypes()
  {
    return implode(',', self::$allowedMimeTypes);
  }
}