<?php

class Api_Spot_Form extends Spot_Form
{
  /**
   * Factory for longitude element
   *
   * @return Lib_Form_Element_Location_Angle_Longitude
   */
  public function getLongitude()
  {
    $element = parent::getLongitude();
    if (!$this->_object->getId()) {
      $element->setRequired(true);
    }
    return $element;
  }

  /**
   * Factory for latitude element
   *
   * @return Lib_Form_Element_Location_Angle_Latitude
   */
  public function getLatitude()
  {
    $element = parent::getLatitude();
    if (!$this->_object->getId()) {
      $element->setRequired(true);
    }
    return $element;
  }
}