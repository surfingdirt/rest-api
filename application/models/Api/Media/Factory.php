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
    /**
     * This is a shitty architecture:
     * you need to know the type of media (photo/video) in order
     * to instantiate it correctly (Media_Item_Photo_Row vs
     * Media_Item_Video_Row). But you don't know the type
     * until after it is instantiated.
     *
     * Workaround: load the object normally in the controller,
     * and once we get it here, fork depending on the albumType to
     * instantiate the correct object, in order to read it.
     */
    $table = new Api_Media_Item();
    $result = $table->find($id);
    if (empty($result) || !$neutralObject = $result->current()) {
      throw new Api_Exception_NotFound();
    }

    $object = self::buildItemByIdAndMediaType($neutralObject->getId(), $neutralObject->getMediaType());
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