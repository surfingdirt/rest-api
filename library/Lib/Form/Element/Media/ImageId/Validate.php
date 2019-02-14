<?php
class Lib_Form_Element_Media_ImageId_Validate extends Zend_Validate_Abstract
{
  const DOES_NOT_EXIST = 'doesNotExist';
  const DUPLICATED_IMAGE_ID = 'duplicatedImageId';

  public function isValid($value, $context = NULL)
  {
    // Does the image exist?
    $imageTable = new Api_Image();
    $imageRow = $imageTable->find($value)->current();
    if (is_null($imageRow)) {
      $this->_error(self::DOES_NOT_EXIST);
      return false;
    }

    // Was it linked to a photo already?
    $mediaTable = new Media_Item_Photo();
    $where = $mediaTable->getAdapter()->quoteInto('imageId = ?', $value);
    $mediaRow = $mediaTable->fetchRow($where);
    if (!$mediaRow) {
      return true;
    }

    if (!isset($context['id']) && $mediaRow) {
      // Creating an object but another one uses this image already
      $this->_error(self::DUPLICATED_IMAGE_ID);
      return false;
    }

    if (isset($context['id']) && $context['id'] !== $mediaRow->getId()) {
      // Another existing object uses this image already
      $this->_error(self::DUPLICATED_IMAGE_ID);
      return false;
    }

    return true;
  }
}