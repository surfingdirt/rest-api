<?php

class Api_Reaction extends Data
{
  protected $_name = Constants_TableNames::REACTION;
  protected $_itemType = 'reaction';

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

  const ITEMTYPE_ALBUM = 'mediaalbum';
  const ITEMTYPE_COMMENT = 'comment';
  const ITEMTYPE_PHOTO = 'photo';
  const ITEMTYPE_VIDEO = 'video';

  public static $itemTypes = array(
    self::ITEMTYPE_ALBUM,
    self::ITEMTYPE_COMMENT,
    self::ITEMTYPE_PHOTO,
    self::ITEMTYPE_VIDEO,
  );

  public static function getReactionsListCacheId($itemType, $itemId)
  {
    $id = 'reactions_' . $itemType . str_replace('-', '', $itemId);
    return $id;
  }

  public static function fetchReactions($itemType, $itemId)
  {
    $table = new Api_Reaction();
    $adapter = $table->getAdapter();
    $where = $adapter->quoteInto("itemType = '$itemType' AND itemId = ?", $itemId);

    if (!ALLOW_CACHE) {
      $reactions = $table->fetchAll($where);
      return $reactions;
    }

    $cacheId = self::getReactionsListCacheId($itemType, $itemId);
    $cache = $table->getCache();
    $result = $cache->load($cacheId);
    if ($result) {
      $result->setTable($table);
      $result->rewind();
      return $result;
    }

    $reactions = $table->fetchAll($where);
    $table->saveDataInCache($cache, $reactions, $cacheId);
    return $reactions;
  }

  public static function clearReactionCacheFor($reaction)
  {
    $cacheId = self::getReactionsListCacheId($reaction->itemType, $reaction->itemId);
    $table = new Api_Reaction();
    $cache = $table->getCache();
    $cache->remove($cacheId);
  }

  /**
   * This method will retrieve reactions for a given object and return an object with the count of each type, as well
   * as the list of reactions (with ids) for the current user.
   * @param $parent
   * @return array
   * @throws Zend_Db_Table_Exception
   */
  public static function getReactions($parent, $user)
  {
    $itemType = $parent->getItemType();
    $itemId = $parent->getId();

    $reactions = self::fetchReactions($itemType, $itemId)->toArray();

    function getReactionCountPerType($acc, $reaction) {
      $type = $reaction['type'];
      if (!isset($acc[$type])) {
        $acc[$type] = 1;
      } else {
        $acc[$type] += 1;
      }
      return $acc;
    }
    $countPerType = array_reduce($reactions, 'getReactionCountPerType', []);

    function getUserReactions($acc, $reaction) {
      if ($reaction['userId'] === $user->getId()) {
        $type = $reaction['type'];
        $acc[$type] = $reaction['id'];
      }
      return $acc;
    }
    if ($user->isLoggedIn()) {
      $userReactions = array_reduce($reactions, 'getUserReactions', []);
    } else {
      $userReactions = [];
    }

    return [
      'counts' => $countPerType,
      'userReactions' => $userReactions,
    ];
  }
}