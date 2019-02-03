<?php
class Api_Media_Form_Video extends Media_Item_Form
{
  public function getMediaSubType()
  {
    $element = new Lib_Form_Element_Media_SubType('mediaSubType');
    $element->setRequired(true);
    $element->addValidator(new Lib_Form_Element_Media_SubType_VideoValidate());
    return $element;
  }
}