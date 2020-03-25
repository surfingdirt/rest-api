<?php

class NotificationsController extends Api_Controller_Action
{
  public function listAction()
  {
    list($viewRange, $from, $until, $limit, $useCache) = $this->_getContext();
    $items = Item::getFeedItems($from, $until, $this->_user, $this->_acl, $limit);

    $this->view->output = array(
      'range' => $viewRange,
      'from' => $from,
      'items' => $items,
    );
  }

  protected function _getContext()
  {
    $useCache = false && ALLOW_CACHE;
    list($range, $viewRange, $from, $until) = $this->_getRange();

    if ($this->_user->getRoleId() == User::STATUS_ADMIN) {
      $hardFrom = $this->_request->getParam('from');
      if (!empty($hardFrom)) {
        $from = $hardFrom;
        $useCache = false;
      }

      $hardUntil = $this->_request->getParam('until');
      if (!empty($hardUntil)) {
        $until = $hardUntil;
        $useCache = false;
      }

      $limit = $this->_request->getParam('limit', MAX_NOTIFICATION_ITEMS_ADMIN);
    } else {
      $limit = MAX_NOTIFICATION_ITEMS_USERS;
    }

    return [$viewRange, $from, $until, $limit, $useCache];
  }

  public function oldListAction()
  {
    $useCache = false && ALLOW_CACHE;
    list($range, $viewRange, $from, $until) = $this->_getRange();

    if ($this->_user->getRoleId() == User::STATUS_ADMIN) {
      $hardFrom = $this->_request->getParam('from');
      if (!empty($hardFrom)) {
        $from = $hardFrom;
        $useCache = false;
      }

      $hardUntil = $this->_request->getParam('until');
      if (!empty($hardUntil)) {
        $until = $hardUntil;
        $useCache = false;
      }

      $limit = $this->_request->getParam('limit', MAX_NOTIFICATION_ITEMS_ADMIN);
    } else {
      $limit = MAX_NOTIFICATION_ITEMS_USERS;
    }

    if ($useCache) {
      $cache = $this->_user->getCache();
      $cacheId = Item::getNewItemsCacheId($this->_user->getId(), $viewRange, $limit);
      $filteredItems = $cache->load($cacheId);
      if (!$filteredItems) {
        $allNewItems = Item::getAllItemsPostedSince($from, $until, $this->_user, $this->_acl, $limit);
        $filteredItems = Item::filterOutItems($allNewItems, User_Notification::MEDIUM_HOMEPAGE, $this->_user, true);
        $this->_user->getTable()->saveDataInCache($cache, $filteredItems, $cacheId, 200);
      } else {
        $filteredItems = Item::wakeupItems($filteredItems);
      }
    } else {
      $allNewItems = Item::getAllItemsPostedSince($from, $until, $this->_user, $this->_acl, $limit);
      $filteredItems = Item::filterOutItems($allNewItems, User_Notification::MEDIUM_HOMEPAGE, $this->_user, true);
    }

//    $filteredItems = $this->_addPrivateMessagesToFilteredItems($filteredItems, $from);

    $list = $this->_flattenList($filteredItems);

    $this->view->output = array(
      'range' => $viewRange,
      'from' => $from,
      'newItems' => $list
    );
  }

  protected function _getRange()
  {
    $range = $this->_request->getParam('range');
    $from = $this->_request->getParam('from');

    $until = new Zend_Date(Utils::date("timestamp"));
    $until = $until->get('YYYY-MM-dd HH:mm:ss');

    $useCache = true && ALLOW_CACHE;
    $viewRange = 'overLastWeek';

    if ($from) {
      $viewRange = 'custom';
    } else {
      switch ($range) {
        case 'lastDay':
          $from = $from->subDay(1)->get('YYYY-MM-dd HH:mm:ss');
          $viewRange = 'overLastDay';
          break;
        case 'lastWeek':
          $from = $from->subWeek(1)->get('YYYY-MM-dd HH:mm:ss');
          $viewRange = 'overLastWeek';
          break;
        case 'lastMonth':
          $from = $from->subMonth(1)->get('YYYY-MM-dd HH:mm:ss');
          $viewRange = 'overLastMonth';
          break;
        case 'lastVisit':
          $from = Zend_Auth::getInstance()->getIdentity()->lastLogin;
          if (empty($from)) {
            $from = new Zend_Date(Utils::date("timestamp"));
            $from = $from->subMonth(1)->get('YYYY-MM-dd HH:mm:ss');
          }
          $viewRange = 'sinceLastVisit';
          break;
      }
    }
    return array($range, $viewRange, $from, $until);
  }

  protected function _addPrivateMessagesToFilteredItems($filteredItems, $from)
  {
    $newMessages = $this->_user->getNewUnreadPrivateMessages($from);
    foreach ($newMessages as $newMessage) {
      $insert = array(
        'parent' => array(
          'object' => $newMessage,
          'dataType' => Constants_DataTypes::PRIVATEMESSAGE,
        ),
        'children' => array(),
      );
      if (!isset($filteredItems['newElementsAndMetadata'])) {
        $filteredItems['newElementsAndMetadata'] = array();
      }
      array_unshift($filteredItems['newElementsAndMetadata'], $insert);
    }

    $oldMessages = $this->_user->getOldUnreadPrivateMessages($from);
    foreach ($oldMessages as $oldMessage) {
      $insert = array(
        'parent' => array(
          'object' => $oldMessage,
          'dataType' => Constants_DataTypes::PRIVATEMESSAGE,
        ),
        'children' => array(),
      );
      if (!isset($filteredItems['oldElementsAndMetadata'])) {
        $filteredItems['oldElementsAndMetadata'] = array();
      }
      array_unshift($filteredItems['oldElementsAndMetadata'], $insert);
    }

    return $filteredItems;
  }

  /**
   * Takes a list of objects and returns a list of array objects
   * in the format that the client expects.
   *
   */
  protected function _flattenList($filteredItems)
  {
    $return = array(
      'newElementsAndMetadata' => array(),
      'oldElementsAndNewMetadata' => array()
    );


    $accessors = array();

    foreach ($filteredItems['newElementsAndMetadata'] as $index => $newElement) {
      $item = array();

      if (!isset($accessors[$newElement['parent']['dataType']])) {
        list($dummy, $accessor) = $this->_mapResource($newElement['parent']['dataType']);
        $accessors[$newElement['parent']['dataType']] = $accessor;
      }
      $accessor = $accessors[$newElement['parent']['dataType']];
      try {
        $item['parent'] = $accessor->getObjectData($newElement['parent']['object']);
      } catch(Exception $e) {
        throw $e;
      }
      $item['parent']['itemType'] = $newElement['parent']['dataType'];

      $children = array();
      foreach ($newElement['children'] as $child) {
        list($dummy, $accessor) = $this->_mapResource($child['dataType']);
        if (isset($child['object'])) {
          $children[] = $accessor->getObjectData($child['object']);
        }

      }
      $item['children'] = $children;

      $return['newElementsAndMetadata'][] = $item;
    }

    foreach ($filteredItems['oldElementsAndNewMetadata'] as $oldElement) {
      $item = array();
      list($dummy, $accessor) = $this->_mapResource($oldElement['parent']['dataType']);

      $item['parent'] = $accessor->getObjectData($oldElement['parent']['object']);
      $item['parent']['itemType'] = $oldElement['parent']['dataType'];
      $item['children'] = $oldElement['children'];

      $return['oldElementsAndNewMetadata'][] = $item;
    }

    return $return;
  }

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