<?php

class Lib_Translate
{
  const ORIGINAL = 'original';

  public static function isTranslatedField($key) {
    return in_array($key, ['bio', 'content', 'description', 'title']);
  }

  public static function setAsOriginal(&$content) {
    $content[0][self::ORIGINAL] = true;
  }

  public static function encodeField($content) {
    return json_encode($content);
  }

  public static function cmp($a, $b) {
    if (!isset($a['locale']) || !isset($b['locale'])) {
      throw new Lib_Exception("Bad translation format");
    }

    return $a['locale'] > $b['locale'] ? 1 : -1;
  }

  public static function decodeField($content)
  {
    $decoded = json_decode($content, true);
    if (!is_array($decoded)) {
      if (!is_null($decoded)) {
        Globals::getLogger()->log('Could not decode translated field: ' . $content);
      }
      return $decoded;
    }

    usort($decoded, "Lib_Translate::cmp");
    return $decoded;
  }
}