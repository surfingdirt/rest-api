<?php

class FeedController extends Api_Controller_Action
{
  public function listAction()
  {
    list($viewRange, $from, $until, $limit, $useCache) = $this->_getContext();
    $items = $this->_table->getFeedItems($from, $until, $this->_user, $this->_acl, $limit);

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