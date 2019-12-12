<?php
class Lib_Validate_Password extends Zend_Validate_Abstract
{
  const MIN_LENGTH = 8;

  const TOO_SHORT = 'tooShort';

  public function isValid($value)
  {
    if (!$value || strlen($value) < self::MIN_LENGTH) {
      $this->_error(self::TOO_SHORT);
      return false;
    }

    return true;
  }
}