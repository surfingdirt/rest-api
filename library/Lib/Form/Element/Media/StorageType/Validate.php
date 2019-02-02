<?php
class Lib_Form_Element_Media_StorageType_Validate extends Zend_Validate_Abstract
{
  const INVALID_TYPE = 'invalidType';

  public function isValid($value, $context = NULL)
  {
    if (is_null($value) || !in_array($value, Lib_Storage::$validTypes)) {
      $this->_error(self::INVALID_TYPE);
      return false;
    }

    return true;
  }
}