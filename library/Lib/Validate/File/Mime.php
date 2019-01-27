<?php

class Lib_Validate_File_Mime extends Zend_Validate_Abstract
{
  const BAD_MIME = 'badMimeType';

  protected $_allowed = array();

  /**
   * @var array
   */
  protected $_messageTemplates = array(
    self::BAD_MIME => "badMimeType",
  );

  public function setAllowedMimeTypes($mimeTypes)
  {
    $this->_allowed = $mimeTypes;
  }

  public function isValid($value, $content = array())
  {
    $imageInfo = getimagesize($content['tmp_name']);
    if (!$imageInfo || !in_array($imageInfo["mime"], $this->_allowed)) {
      $this->_error(self::BAD_MIME);
      return false;
    }

    return true;
  }
}