<?php
class Api_Media_Form_Photo extends Media_Item_Form
{
  public function getMediaSubType()
  {
    $element = new Lib_Form_Element_Media_SubType('mediaSubType');
    $element->addValidator(new Lib_Form_Element_Media_SubType_PhotoValidate());
    return $element;
  }

  protected function _buildElements()
  {
    $elements = parent::_buildElements();

    $elements['imageId'] = $this->getImageId();

    return $elements;
  }

  public function getImageId($required = true)
  {
    $element = new Lib_Form_Element_Media_ImageId('imageId');
    $element->addValidator(new Lib_Form_Element_Media_ImageId_Validate());
    $element->setRequired($required);
    return $element;
  }

}