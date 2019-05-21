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
    'albumCreation',
    'actions',
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
  public function getObjectData($object, $action = 'list', $requestParams = null)
  {
    if (!$requestParams) {
      $requestParams = array();
    }
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
        $mediaItems[] = $mediaAccessor->getObjectData($media);
      }
      $ret['media'] = $this->_restrictMediaItems($mediaItems, $requestParams);
    }

    return $ret;
  }

  protected function _restrictMediaItems(array $mediaItems, array $params)
  {
    $start = isset($params['start']) ? (int)$params['start'] : 0;
    $start = max($start, 0);

    $count = isset($params['count']) ? (int)$params['count'] : MEDIA_PER_PAGE;
    $count = max($count, 0);

    $dir = (isset($params['dir']) && $params['dir'] == 'ASC') ? 'ASC' : 'DESC';
    if ($dir == 'ASC') {
      usort($mediaItems, array($this, '_sortByDateAsc'));
    } else {
      usort($mediaItems, array($this, '_sortByDateDesc'));
    }

    $return = [];
    for($i = $start; $i <= $start + $count; $i++) {
      if ($i >= sizeof($mediaItems)) {
        break;
      }
      $return[] = $mediaItems[$i];
    }
    return $return;
  }

  static protected function _getComparableDate($date)
  {
    $parts = explode('.', $date);
    return 1000 * strtotime($parts[0]) + (int)($parts[1]);
  }

  protected function _sortByDateDesc($a, $b)
  {
    $a = self::_getComparableDate($a['date']);
    $b = self::_getComparableDate($b['date']);

    if ($a == $b) {
      return 0;
    }
    return ($a < $b) ? 1 : -1;
  }

  protected function _sortByDateAsc($a, $b)
  {
    $a = self::_getComparableDate($a['date']);
    $b = self::_getComparableDate($b['date']);

    if ($a == $b) {
      return 0;
    }
    return ($a < $b) ? -1 : 1;
  }
}