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
    'albumContributions',
    'albumCreation',
    'albumVisibility',
    'actions',
  );

  public $memberCreateAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'albumContributions' => 'albumContributions',
    'albumVisibility' => 'albumVisibility',
    );

  public $adminCreateAttributes = array(
    'albumCreation' => 'albumCreation',
    'albumContributions' => 'albumContributions',
    'albumVisibility' => 'albumVisibility',
    'albumType' => 'albumType'
  );

  public $ownWriteAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'albumContributions' => 'albumContributions',
    'albumVisibility' => 'albumVisibility',
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

    if ($action == 'get' || $action == 'list') {
      // Add media info for get operations
      $mediaItems = array();
      $itemSet = $object->getItemSet();
      $mediaAccessor = new Api_Media_Accessor($this->_user, $this->_acl);
      foreach ($itemSet as $item) {
        $media = Media_Item_Factory::buildItem($item['id'], $item['mediaType']);
        $mediaItems[] = $mediaAccessor->getObjectData($media);
      }
      $ret['media'] = $this->_restrictMediaItems($mediaItems, $requestParams);
      $ret['itemCount'] = sizeof($itemSet);
    }

    return $ret;
  }

  public function getActions($object)
  {
    $actions = parent::getActions($object);
    $actions['add'] = $object->canBeAddedToBy($this->_user, $this->_acl);
    return $actions;
  }

  protected function _restrictMediaItems(array $mediaItems, array $params)
  {
    $start = isset($params['startItem']) ? (int)$params['startItem'] : 0;
    $start = max($start, 0);

    $count = isset($params['countItems']) ? (int)$params['countItems'] : MEDIA_PER_PAGE;
    $count = max($count, 0);

    $dir = (isset($params['dirItems']) && $params['dirItems'] == 'ASC') ? 'ASC' : 'DESC';
    if ($dir == 'ASC') {
      usort($mediaItems, array($this, '_sortByDateAsc'));
    } else {
      usort($mediaItems, array($this, '_sortByDateDesc'));
    }

    $return = [];
    for($i = $start; $i < $start + $count; $i++) {
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