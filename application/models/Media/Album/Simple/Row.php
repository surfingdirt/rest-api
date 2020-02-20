<?php

class Media_Album_Simple_Row extends Media_Album_Row
{
  public $page;

  public function __construct(array $config = array(), $page = 1)
  {
    parent::__construct($config);
    $this->page = $page;
  }

  protected function _getItems()
  {
    $cache = $this->getCache();
    $cacheId = $this->getItemsCacheId();

    $albumItems = null;
    if (ALLOW_CACHE) {
      $albumItems = $cache->load($cacheId);
    }

    if (!$albumItems) {
      $db = Globals::getMainDatabase();
      $where = $db->quoteInto('WHERE m.albumId = ?', $this->id);
      $sql = "
				SELECT m.id, m.mediaType
				FROM " . Constants_TableNames::MEDIA . " m
				$where
				ORDER BY m.id DESC
			";
      $albumItems = $db->query($sql)->fetchAll();
    }

    if (ALLOW_CACHE) {
      $this->getTable()->saveDataInCache($cache, $albumItems, $cacheId);
    }

    return $albumItems;
  }
}
