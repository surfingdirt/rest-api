<?php
class Lib_Form_Element_Media_SubType_PhotoValidate extends Zend_Validate_Abstract
{
  const INVALID_TYPE = 'invalidType';

  public function isValid($value, $context = NULL)
  {
    return true;
  }
}