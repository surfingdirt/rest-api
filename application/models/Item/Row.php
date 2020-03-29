<?php

class Item_Row extends Cache_Object_Row
{
  const NOTIFICATION_ANNOUNCE = 'announce';
  const NOTIFICATION_SILENT = 'silent';

  /**
   * Get the cache for this object
   *
   * @return Zend_Cache_Core
   */
  public function getCache()
  {
    return Globals::getGlobalCache();
  }

  public function clearCache()
  {
    $cache = $this->getCache();
    $cacheIds = array(
      Api_Feed::FEED_ITEMS_CACHE_ID,
    );

    foreach ($cacheIds as $cacheId) {
      $cache->remove($cacheId);
    }
  }


}