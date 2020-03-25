<?php
class Api_Feed
{
  public function getFeedItems($from, $until, User_Row $user, Lib_Acl $acl, $maxItems)
  {
    $db = Globals::getMainDatabase();
    $userId = $user->getId();
    $table = Constants_TableNames::ITEM;
    $silent = Item_Row::NOTIFICATION_SILENT;
    $valid = Data::VALID;

    $limit = empty($maxItems) ? MAX_NOTIFICATION_ITEMS_USERS : $maxItems;

    $from = $db->quote($from);
    $from = ' AND date >= ' . $from;

    $until = '';
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
        LIMIT $limit        
    ";
    $feedItems = $db->fetchAll($sql);
    return $feedItems;
  }


}