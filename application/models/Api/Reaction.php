<?php

class Api_Reaction extends Data
{
  protected $_name = Constants_TableNames::REACTION;

  protected $_rowClass = 'Api_Reaction_Row';
  protected $_rowsetClass = 'Api_Reaction_Rowset';

  const TYPE_ANGRY = 'angry';
  const TYPE_IMPRESSED = 'impressed';
  const TYPE_LAUGHING = 'laughing';
  const TYPE_SAD = 'sad';
  const TYPE_SCARED = 'scared';
  const TYPE_STOKED = 'stoked';

  public static $reactionTypes = array(
    self::TYPE_ANGRY,
    self::TYPE_IMPRESSED,
    self::TYPE_LAUGHING,
    self::TYPE_SAD,
    self::TYPE_SCARED,
    self::TYPE_STOKED,
  );

  const ITEMTYPE_ALBUM = 'album';
  const ITEMTYPE_COMMENT = 'comment';
  const ITEMTYPE_PHOTO = 'photo';
  const ITEMTYPE_VIDEO = 'video';

  public static $itemTypes = array(
    self::ITEMTYPE_ALBUM,
    self::ITEMTYPE_COMMENT,
    self::ITEMTYPE_PHOTO,
    self::ITEMTYPE_VIDEO,
  );
}