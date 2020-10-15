<?php

class FeedController extends Api_Controller_Action
{
  const FEED_DURATION_IN_WEEKS = 4;

  public function listAction()
  {
    $from = (new Zend_Date(Utils::date("timestamp")))->subWeek(FEED_DURATION_IN_WEEKS)->get('YYYY-MM-dd HH:mm:ss');
    $until = (new Zend_Date(Utils::date("timestamp")))->get('YYYY-MM-dd HH:mm:ss');
    $limit = MAX_NOTIFICATION_ITEMS_USERS;
    $skipCache = false;

    $feed = $this->_table;
    $cache = Globals::getGlobalCache();
    $cacheId = Api_Feed::FEED_ITEMS_CACHE_ID;
    if (!ALLOW_CACHE || $skipCache) {
      $items = $feed->listFeedItems($from, $until, $limit);
    } else {
      $items = $cache->load($cacheId);
      if (!$items) {
        $items = $feed->listFeedItems($from, $until, $limit);
        $cache->save($items, $cacheId);
      }
    }

    $this->view->output =
      array(
        'from' => $from,
        'until' => $until,
        'items' => $items,
      );
  }

//  protected function _mapResource($key)
//  {
//    // TODO: implement this in order to not define an empty Api_Feed_Accessor
//  }

  public function getAction()
  {
    throw new Api_Exception_BadRequest();
  }

  public function putAction()
  {
    throw new Api_Exception_BadRequest();
  }

  public function postAction()
  {
    throw new Api_Exception_BadRequest();
  }

  public function deleteAction()
  {
    throw new Api_Exception_BadRequest();
  }
}