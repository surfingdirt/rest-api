<?php

class VideoUrlBuilder
{
  public static function buildUrl($mediaSubType, $vendorKey)
  {
    switch ($mediaSubType) {
      case Media_Item_Video::SUBTYPE_YOUTUBE:
        $uri = 'https://www.youtube.com/watch?v=' . $vendorKey;
        break;
      case Media_Item_Video::SUBTYPE_VIMEO:
        $uri = 'https://vimeo.com/' . $vendorKey;
        break;
      case Media_Item_Video::SUBTYPE_DAILYMOTION:
        $uri = 'https://www.dailymotion.com/video/' . $vendorKey;
        break;
      case Media_Item_Video::SUBTYPE_FACEBOOK:
        $uri = 'https://www.facebook.com/' . $vendorKey;
        break;
      case Media_Item_Video::SUBTYPE_INSTAGRAM:
        $uri = 'https://www.instagram.com/p/' . $vendorKey;
        break;
      default:
        throw new Lib_Exception_Media("Unsupported mediaSubType '$mediaSubType' for vendor key '$vendorKey'");
        break;
    }
    return $uri;
  }
}