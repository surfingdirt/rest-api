<?php

class FeedController extends Api_Controller_Action
{
  public function listAction()
  {
    $feed = $this->_table;
    $from = (new Zend_Date(Utils::date("timestamp")))->subWeek(1)->get('YYYY-MM-dd HH:mm:ss');
    $until = (new Zend_Date(Utils::date("timestamp")))->get('YYYY-MM-dd HH:mm:ss');
    $limit = MAX_NOTIFICATION_ITEMS_USERS;

    $dbItems = $feed->getDbItems($from, $until, $this->_user, $this->_acl, $limit);
    $feed->buildLevels($dbItems);
    $feed->mergeLevels();
    $items = $feed->getSortedItems();
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