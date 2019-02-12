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

  /**
   * Builds and returns an array of form elements
   *
   * @return array
   */
  protected function _buildElements()
  {
    $elements = parent::_buildElements();

    $elements['vendorKey'] = $this->getVendorKey();

    return $elements;
  }

  public function getVendorKey($required = true)
  {
    $element = new Lib_Form_Element_Media_Key('vendorKey');
    $element->setRequired($required);
    return $element;
  }
}