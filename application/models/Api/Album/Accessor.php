<?php

class Api_Album_Accessor extends Api_Data_Accessor
{
  public $publicReadAttributes = array(
    'id',
    'title',
    'description',
    'date',
    'submitter',
    'lastEditor',
    'lastEditionDate',
    'status',
    'albumType',
    'albumAccess',
    'albumCreation'
  );

  public $memberCreateAttributes = array(
    'title' => 'title',
    'description' => 'description');

  public $adminCreateAttributes = array(
    'albumCreation' => 'albumCreation',
    'albumAccess' => 'albumAccess',
    'albumType' => 'albumType'
  );

  public $ownWriteAttributes = array(
    'title' => 'title',
    'description' => 'description'
  );

  /**
   * Performs a read operation
   */
  public function getObjectData($object, $action = 'list', array $requestParams = array())
  {
    $attributes = $this->getReadAttributes($object);
    $ret = array();
    foreach ($attributes as $attr) {
      $ret = $this->_addEntriesForAttribute($attr, $object, $ret);
    }

    if ($action == 'get') {
      // Add media info for get operations
      $mediaItems = array();
      $itemSet = $object->getItemSet();
      $mediaAccessor = new Api_Media_Accessor($this->_user, $this->_acl);
      foreach ($itemSet as $item) {
        $media = Media_Item_Factory::buildItem($item['id'], $item['mediaType']);
        $mediaItems[$media->getDate()] = $mediaAccessor->getObjectData($media);
      }
      $ret['media'] = $this->_restrictMediaItems($mediaItems, $requestParams);
    }

    return $ret;
  }

  protected function _restrictMediaItems(array $mediaItems, array $params)
  {
    $dir = (isset($params['dir']) && $params['dir'] == 'ASC') ? 'ASC' : 'DESC';
    $start = isset($params['start']) ? (int)$params['start'] : 0;
    $count = isset($params['count']) ? (int)$params['count'] : MEDIA_PER_PAGE;

    if ($dir == 'ASC') {
      ksort($mediaItems);
    } else {
      krsort($mediaItems);
    }

    if ($start > 0 && $start - 1 <= count($mediaItems)) {
      array_splice($mediaItems, 0, $start - 1);
    }

    if ($count > 0 && $count <= count($mediaItems)) {
      array_splice($mediaItems, $count);
    }

    $return = array();
    foreach ($mediaItems as $k => $v) {
      $return[] = $v;
    }
    return $return;
  }
}