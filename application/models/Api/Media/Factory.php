<?php

class Api_Media_Factory
{
  public static function createItem($mediaType)
  {
    $table = self::_getTableByMediaType($mediaType);
    $item = $table->createRow();
    return $item;
  }

  public static function buildItemById($id)
  {
    $table = new Media_Item();
    $result = $table->find($id);
    if (empty($result) || !$neutralObject = $result->current()) {
      throw new Api_Exception_NotFound();
    }

    $object = self::buildItemByIdAndMediaType($neutralObject->id, $neutralObject->mediaType);
    return $object;
  }

  public static function buildItemByIdAndMediaType($id, $mediaType)
  {
    $table = self::_getTableByMediaType($mediaType);
    $item = $table->find($id)->current();
    return $item;
  }

  protected static function _getTableByMediaType($mediaType)
  {
    switch ($mediaType) {
      case Media_Item::TYPE_PHOTO:
        $table = new Api_Media_Photo();
        break;
      case Media_Item::TYPE_VIDEO:
        $table = new Api_Media_Video();
        break;
      default:
        throw new Api_Exception_BadRequest(
          "Bad media type: '$mediaType'",
          Api_ErrorCodes::MEDIA_BAD_MEDIA_TYPE);
        break;
    }
    return $table;
  }
}