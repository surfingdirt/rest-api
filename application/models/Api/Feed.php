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

  public function getFeedItems($from, $until, User_Row $user, Lib_Acl $acl, $maxItems, $useCache)
  {
    $db = Globals::getMainDatabase();
    $userId = $user->getId();
    $table = Constants_TableNames::ITEM;
    $silent = Item_Row::NOTIFICATION_SILENT;
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
        AND notification <> '$silent'
        AND status = '$valid'
        $from
        $until
        -- LIMIT $limit        
    ";
    $feedItems = $db->fetchAll($sql);
    return $feedItems;
  }

  public function buildLevels($items)
  {
    $log = [];

    $level1 = $this->_buildLevel1FromItems($items);
    list($level2, $level2Children) = $this->_buildLevel2FromItems($items, $level1);
    $level3 = $this->_buildLevel3FromItems($items, $level2, $level2Children);

    // Filter out elements from level 1 that are silent
    $level1 = array_filter($level1, function($item) {
      return $item['notification'] === Item_Row::NOTIFICATION_ANNOUNCE;
    });


    $levels = [$level1, $level2, $level3];
    return [$levels, $log];
  }

  public function mergeLevels($levels)
  {
    list($level1, $level2, $level3) = $levels;

    $mergedItems = array_merge($level1, $level2, $level3);
    usort($mergedItems, function($a, $b) {
      return strtotime($b['date']) - strtotime($a['date']);
    });
    return $mergedItems;
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

      $id = $item['itemId'];
      $item['children'] = [];
      $level1[$id] = $item;
    }

    return $level1;
  }

  protected function _buildLevel2FromItems($items, $level1)
  {
    $level2 = [];
    $level2Children = [];
    foreach ($items as $item) {
      if (!in_array($item['itemType'], self::TYPES_ALLOWED_IN_LEVEL2)) {
        continue;
      }

      // Only look at elements with a parent
      if (!$item['parentItemId']) {
        continue;
      }

      if (!$this->_checkItemLevel2($level1, $item, $log)) {
        continue;
      }

      // Only store in level2 those items whose parent is not in level1 already
      $parentItemId = $item['parentItemId'];
      if (!isset($level1[$parentItemId])) {
        $id = $item['itemId'];
        $level2Children[$id] = $item;
      }
    }
    foreach ($level2Children as $child) {
      $parentItemId = $child['parentItemId'];
      if (!isset($level2[$parentItemId]) && $child['notification'] === Item_Row::NOTIFICATION_ANNOUNCE) {
        $level2[$parentItemId] = [
          'itemId' => $parentItemId,
          'itemType' => $child['parentItemType'],
          // TODO: this isn't right, we need to store the parent's date as well
          'date' => $child['date'],
          'children' => [],
        ];
      }
      $level2[$parentItemId]['children'][] = $child;
    }

    return [$level2, $level2Children];
  }

  protected function _buildLevel3FromItems($items, $level2, $level2Children)
  {
    $level3 = [];
    foreach ($items as $item) {
      if (!in_array($item['itemType'], self::TYPES_ALLOWED_IN_LEVEL3)) {
        continue;
      }
      if (!$item['parentItemId'] || $item['notification'] === Item_Row::NOTIFICATION_SILENT) {
        continue;
      }

      $parentItemId = $item['parentItemId'];
      if (isset($level2Children[$parentItemId])) {
        // Parent is in level 2 already: no need to report this
        continue;
      }

      $parentItem = $level2Children[$parentItemId];
      $parentItem['children'][] = $item;
      $level3[] = $item;
    }

    return $level3;
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