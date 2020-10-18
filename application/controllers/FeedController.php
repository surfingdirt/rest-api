<?php

class FeedController extends Api_Controller_Action
{
  public function listAction()
  {
    $count = $this->_request->getParam('count', FEED_PAGE_SIZE);
    $offset = $this->_request->getParam('offset', 0);
    $skipCache = false; // For debugging purposes

    $feed = $this->_table;
    $cache = Globals::getGlobalCache();
    $cacheId = Api_Feed::FEED_ITEMS_CACHE_ID . $offset;
    if (!ALLOW_CACHE || $skipCache) {
      $items = $feed->listFeedItems($count, $offset);
    } else {
      $items = $cache->load($cacheId);
      if (!$items) {
        $items = $feed->listFeedItems($count, $offset);
        $cache->save($items, $cacheId);
      }
    }

    $this->view->output =
      array(
        'nextOffset' => $count + $offset,
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