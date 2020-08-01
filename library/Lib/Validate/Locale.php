<?php
class Lib_Validate_Locale extends Zend_Validate_Abstract
{
  public function isValid($value)
  {
    return in_array($value, SUPPORTED_LOCALES);
  }
}