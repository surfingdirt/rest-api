<?php
class Lib_Form_Element_Media_SubType_VideoValidate extends Zend_Validate_Abstract
{
  const INVALID_TYPE = 'invalidType';

  public function isValid($value, $context = NULL)
  {
    if (is_null($value) || !in_array($value, Media_Item_Video::$allowedMediaSubTypes)) {
      $this->_error(self::INVALID_TYPE);
      return false;
    }

    return true;
  }
}