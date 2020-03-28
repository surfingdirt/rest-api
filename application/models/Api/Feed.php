<?php
class Api_Feed
{
  const TYPES_ALLOWED_IN_LEVEL1 = [
    Constants_DataTypes::MEDIAALBUM, Constants_DataTypes::USER,
  ];
  const TYPES_ALLOWED_IN_LEVEL2 = [
    Constants_DataTypes::PHOTO, Constants_DataTypes::VIDEO,
  ];
  const TYPES_ALLOWED_IN_LEVEL3 = [
    Constants_DataTypes::COMMENT,
  ];

  public function __construct()
  {
    $this->_level1 = [];
    $this->_level2 = [];
    $this->_level3 = [];

    $this->_newItems = [];
    $this->_newSubItems = [];
  }

  protected static function _stripItem($item)
  {
    return [
      'date' => $item['date'],
      'itemId' => $item['itemId'],
      'itemType' => $item['itemType'],
    ];
  }

  protected static function _stripRootItem($item)
  {
    $sortDate = sizeof($item['children']) > 0 ? self::_getSortDate($item['children']) : $item['date'];
    $root = [
      'sortDate' => $sortDate,
      'itemId' => $item['itemId'],
      'itemType' => $item['itemType'],
      'children' => $item['children'],
    ];

    return $root;
  }

  protected static function _stripNewParent($item)
  {
    $children = [self::_stripItem($item)];
    return [
      'children' => $children,
      'sortDate' => self::_getSortDate($children),
      'itemId' => $item['parentItemId'],
      'itemType' => $item['parentItemType'],
    ];
  }

  protected static function _attachItemsToParents($childLevel, $parentLevel, $subItems)
  {
    foreach ($childLevel as $item) {
      $parentId = $item['parentItemId'];
      if (isset($parentLevel[$parentId])) {
        $parentLevel[$parentId]['children'][] = self::_stripItem($item);
      } else {
        if (isset($newSubItems[$parentId])) {
          $subItems[$parentId]['children'][] = self::_stripItem($item);
        } else {
          $subItems[$parentId] = self::_stripNewParent($item);
        }
      }
    }

    return [$subItems, $parentLevel];
  }

  protected static function _addSortDate($subItems)
  {
    foreach ($subItems as $subItem) {
      $sortDate = self::_getSortDate($subItem['children']);
      $subItem['sortDate'] = $sortDate;
    }
    return $subItems;
  }

  protected static function _getSortDate($children)
  {
    // The sort date will be that of the most recent of the subItem's children
    $sortDate = '1970-01-01 00:00:00.000';
    foreach ($children as $child) {
      if (strtotime($child['date']) > strtotime($sortDate)) {
        $sortDate = $child['date'];
      }
    }
    return $sortDate;
  }

  public function getDbItems($from, $until, User_Row $user, Lib_Acl $acl, $maxItems)
  {
    $db = Globals::getMainDatabase();
    $userId = $user->getId();
    $table = Constants_TableNames::ITEM;
    $announce = Item_Row::NOTIFICATION_ANNOUNCE;
    $valid = Data::VALID;

    $limit = empty($maxItems) ? MAX_NOTIFICATION_ITEMS_USERS : $maxItems;

    $from = $db->quote($from);
    $from = ' AND date >= ' . $from;

    if (!empty($until)) {
      $until = $db->quote($until);
      $until = ' AND date < ' . $until;
    }

    $sql = "
        SELECT
        *
        FROM $table
        WHERE 1
        AND notification = '$announce'
        AND status = '$valid'
        -- AND id = 1265
        $from
        $until
        ORDER BY date DESC
        -- LIMIT $limit        
    ";
    $feedItems = $db->fetchAll($sql);
    return $feedItems;
  }

  public function getLevels()
  {
    return [$this->_level1, $this->_level2, $this->_level3];
  }

  public function getNewItems()
  {
    return $this->_newItems;
  }

  public function getNewSubItems()
  {
    return $this->_newSubItems;
  }

  public function buildLevels($items)
  {
    $this->_buildLevel1FromItems($items);
    $this->_buildLevel2FromItems($items);
    $this->_buildLevel3FromItems($items);
  }

  public function mergeLevels()
  {
    list($this->_newSubItems, $this->_level2) = self::_attachItemsToParents($this->_level3, $this->_level2, $this->_newSubItems);
    list($this->_newSubItems, $this->_level1) = self::_attachItemsToParents($this->_level2, $this->_level1, $this->_newSubItems);
    $this->_newSubItems = self::_addSortDate($this->_newSubItems);

    $newItems = [];
    foreach($this->_level1 as $id => $item) {
      $newItems[$id] = self::_stripRootItem($item);
    }

    $this->_newItems = $newItems;
  }

  public function getSortedItems()
  {
    $items = array_merge($this->_newSubItems, $this->_newItems);
    usort($items, function($a, $b) {
      return strtotime($a['sortDate']) < strtotime($b['sortDate']);
    });
    return $items;
  }

  protected function _buildLevel1FromItems($items)
  {
    $level1 = [];
    foreach ($items as $item) {
      if (!in_array($item['itemType'], self::TYPES_ALLOWED_IN_LEVEL1)) {
        continue;
      }
      // Only look at elements without a parent
      if ($item['parentItemId']) {
        continue;
      }
      if ($item['notification'] !== Item_Row::NOTIFICATION_ANNOUNCE) {
        continue;
      }

      $id = $item['itemId'];
      $item['children'] = [];
      $level1[$id] = $item;
    }

    $this->_level1 = $level1;
  }

  protected function _buildLevel2FromItems($items)
  {
    $level2 = [];
    foreach ($items as $item) {
      if (!in_array($item['itemType'], self::TYPES_ALLOWED_IN_LEVEL2)) {
        continue;
      }
      // Only look at elements with a parent
      if (!$item['parentItemId']) {
        continue;
      }
      if ($item['notification'] !== Item_Row::NOTIFICATION_ANNOUNCE) {
        continue;
      }

      $id = $item['itemId'];
      $item['children'] = [];
      $level2[$id] = $item;
    }

    $this->_level2 = $level2;
  }

  protected function _buildLevel3FromItems($items)
  {
    $level3 = [];
    foreach ($items as $item) {
      if (!in_array($item['itemType'], self::TYPES_ALLOWED_IN_LEVEL3)) {
        continue;
      }
      // Only look at elements with a parent
      if (!$item['parentItemId']) {
        continue;
      }
      if ($item['notification'] !== Item_Row::NOTIFICATION_ANNOUNCE) {
        continue;
      }

      $id = $item['itemId'];
      // No need to add a children entry because this is the last level
      // $item['children'] = [];
      $level3[$id] = $item;
    }

    $this->_level3 = $level3;
  }

  protected function _checkItemLevel2($parentLevel, $item, &$log)
  {
    $allowedChildrenByParent = [
      Constants_DataTypes::MEDIAALBUM => [Constants_DataTypes::PHOTO, Constants_DataTypes::VIDEO],
      Constants_DataTypes::PHOTO => [Constants_DataTypes::COMMENT],
      Constants_DataTypes::USER => [],
    ];

    $parentItemId = $item['parentItemId'];
    // Note: this returns true because in level2 we want to display the children of things that might not have been announced
    if (!isset($parentLevel[$parentItemId])) {
      return true;
    }

    $parentItem = $parentLevel[$parentItemId];
    $allowedChildren = $allowedChildrenByParent[$parentItem['itemType']];
    if (!in_array($item['itemType'], $allowedChildren)) {
      $log[] = sprintf("Unexpected child of type '%s' and id '%s' for parent of type '%s' and id '%s",
        $item['itemType'],
        $item['itemId'],
        $parentItem['itemType'],
        $parentItem['itemId']
      );
      return false;
    }

    return true;
  }

  protected function _hydrateObject($item)
  {
    $obj = new stdClass();
    $obj->type = $item['itemType'];
    $obj->id = $item['itemId'];
    return $obj;
  }

}