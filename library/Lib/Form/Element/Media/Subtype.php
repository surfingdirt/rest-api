<?php
abstract class Lib_Form_Element_Media_Subtype extends Zend_Form_Element_Text
{
  const INVALID_SUBTYPE = 'invalidSubtype';

  abstract protected static function getValidSubtypes();

  public function isValid($value, $context = NULL)
  {
    if (!in_array($value, self::getValidSubtypes())) {
      $this->addError(self::INVALID_SUBTYPE);
      return false;
    }

    return true;
  }
}