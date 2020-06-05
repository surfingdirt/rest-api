<?php
class Api
{
  public static function getResources()
  {
    return array(
      'album' => 'Album',
      'comments' => 'Comment',
      'comment' => 'Comment',
      'feed' => 'Feed',
      'media' => 'Media',
      'mediaalbum' => 'Album',
      'notifications' => 'Notification',
      'photo' => 'Media',
      'reaction' => 'Reaction',
      'user' => 'User',
      'useralbum' => 'Album',
      'video' => 'Media',
    );
  }

  public static function getReactionResources()
  {
    return array(
      Api_Reaction::ITEMTYPE_ALBUM => 'Album',
      Api_Reaction::ITEMTYPE_COMMENT => 'Comment',
      Api_Reaction::ITEMTYPE_PHOTO => 'Media',
      Api_Reaction::ITEMTYPE_VIDEO => 'Media',
    );
  }
}