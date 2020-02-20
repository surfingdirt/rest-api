<?php

class AlbumController extends Api_Controller_Action
{
  public $listCount = 10;

  public $listDir = 'desc';
  public $listKey = 'lastEditionDate';
  public $allowedListKeys = array('id', 'date', 'lastEditionDate');

  protected function _preObjectCreation($object, $data)
  {
    $object->id = Utils::uuidV4();
    $object->albumType = Media_Album::TYPE_SIMPLE;
    $object->albumContributions = Media_Album::CONTRIBUTIONS_PUBLIC;
    $object->albumVisibility = Media_Album::VISIBILITY_VISIBLE;
    $object->albumCreation = Media_Album::CREATION_USER;
    $object->setNotification(true);
  }

  public function deleteAction()
  {
    $id = $this->_request->getParam('id');
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $result = $this->_table->find($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }

    if ($object->albumType != Media_Album::TYPE_SIMPLE) {
      throw new Api_Exception_BadRequest('Album not deletable', Api_ErrorCodes::NON_STATIC_ALBUM_CANNOT_BE_DELETED);
    }

    $itemSet = $object->getItemSet();
    if (!$itemSet || count($itemSet) > 0) {
      throw new Api_Exception_BadRequest('Album not empty', Api_ErrorCodes::STATIC_ALBUM_NOT_EMPTY);
    }

    if (!$object->isDeletableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $this->_preObjectDelete($object);
    if ($status = $this->_accessor->deleteObject($object)) {
      $this->_postObjectDelete($object);
    }
    $this->view->output = array('status' => $status);
  }

  public function listAction()
  {
    $count = $this->getRequest()->getParam('count', $this->listCount);
    $start = $this->getRequest()->getParam('start', $this->listStart);
    $dir = $this->_getDir();
    $sort = $this->_getSort();

    $where = $this->_getWhereClause($this->_user);

    $results = $this->_getAllObjects($where, $sort, $dir, $count, $start);

    $this->view->output = $results;
  }

  protected function _getWhereClause(User_Row $user)
  {
    $skipAlbums = $this->getRequest()->getParam('skipAlbums', array());

    $adapter = $this->_table->getAdapter();

    if (in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))) {
      $return = '1';
    } else {
      $return = $adapter->quoteInto('status = ?', Data::VALID);
    }

    $return .= $adapter->quoteInto(' AND albumType = ?', Media_Album::TYPE_SIMPLE);
    $return .= $adapter->quoteInto(' AND albumVisibility = ?', Media_Album::VISIBILITY_VISIBLE);

    if (sizeof($skipAlbums) > 0) {
      $list = implode(', ', array_map(function($item) use ($adapter) {
        return $adapter->quoteInto('?', $item);
      }, $skipAlbums));

      if ($list) {
        $return .= " AND id NOT IN ($list)";
      }
    }

    return $return;
  }

  protected function _getAllObjects($where, $sort = null, $dir = null, $count = null, $start = null)
  {
    $cacheId = Api_Album::ALBUM_LIST_CACHE_ID;
    $cache = Globals::getGlobalCache();
    $action = $this->_request->getActionName();
    $params = $this->_request->getParams();
    $results = null;

    if (ALLOW_CACHE) {
      $results = $cache->load($cacheId);
    }

    if (!$results) {
      $albums = parent::_getAllObjects($where, $sort, $dir, $count, $start);
      $results = array();
      foreach ($albums as $album) {
        $results[] = $this->_accessor->getObjectData($album, $action, $params);
      }
      if (ALLOW_CACHE) {
        $cache->save($results, $cacheId);
      }
    }

    return $results;
  }
}