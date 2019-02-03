<?php
class Api_Media_Form_Photo extends Media_Item_Form
{
  public function getMediaSubType()
  {
    $element = new Lib_Form_Element_Media_SubType('mediaSubType');
    $element->addValidator(new Lib_Form_Element_Media_SubType_PhotoValidate());
    return $element;
  }
}