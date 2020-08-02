<?php
class Lib_Validate_Locale extends Zend_Validate_Abstract
{
  public function isValid($value)
  {
    $valid = in_array($value, explode(',',SUPPORTED_LOCALES));

    if (!$valid) {
      $this->_error('invalidLocale');
    }

    return $valid;

  }
}