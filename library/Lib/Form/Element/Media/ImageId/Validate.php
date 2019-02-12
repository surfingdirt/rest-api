<?php
class Lib_Form_Element_Media_ImageId_Validate extends Zend_Validate_Abstract
{
  const DOES_NOT_EXIST = 'doesNotExist';

  public function isValid($value, $context = NULL)
  {
    $table = new Api_Image();
    $imageRow = $table->find($value)->current();

    if (is_null($imageRow)) {
      $this->_error(self::DOES_NOT_EXIST);
      return false;
    }

    return true;
  }
}