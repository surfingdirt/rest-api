<?php

class Media_Item_Video extends Media_Item
{
  const SUBTYPE_DAILYMOTION = 'dailymotion';
  const SUBTYPE_FACEBOOK = 'facebook';
  const SUBTYPE_INSTAGRAM = 'instagram';
  const SUBTYPE_VIMEO = 'vimeo';
  const SUBTYPE_YOUTUBE = 'youtube';

  public static $allowedMediaSubTypes = array(
    self::SUBTYPE_DAILYMOTION,
    self::SUBTYPE_FACEBOOK,
    self::SUBTYPE_INSTAGRAM,
    self::SUBTYPE_VIMEO,
    self::SUBTYPE_YOUTUBE,
  );

  /**
   * Name of the class representing a row
   *
   * @var string
   */
  protected $_rowClass = 'Media_Item_Video_Row';

  protected $_itemType = 'video';

  /**
   * Returns the regex used to make sure a correct video html code was submitted
   *
   * @return string
   */
  public static function getCleanVideoCodeRegex()
  {
    $regex = '#<div class="([a-z]+)-embed"><span class="width">([0-9]{2,3})</span><span class="height">([0-9]{2,3})</span>([A-Za-z0-9\-_]+)</div>#';
    return $regex;
  }
}