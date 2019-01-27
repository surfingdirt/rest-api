<?php

class Api_Checkin_Form extends Data_Form
{
  protected function _setup()
  {
    $elements = array(
      'spot' => $this->getSpot(),
      'checkinDate' => $this->getCheckinDate(),
      'checkinDuration' => $this->getCheckinDuration(),
    );
    $this->addElements($elements);
  }

  public function getSpot()
  {
    $element = new Lib_Form_Element_Spot('spot', true);
    $element->setLabel(ucfirst(Globals::getTranslate()->_('spot')));
    return $element;
  }

  public function getCheckinDate()
  {
    // validates a Y-m-d H:i:s GMT that must be more recent than now() - 20s (processing margin)
    $element = new Lib_Form_Element_DateTime_Simple('checkinDate');
    $validator = new Lib_Validate_DateTime_Simple();
    $validator->setPastAllowed(false);
    $element->addValidator($validator)
      ->setRequired();

    return $element;
  }

  public function getCheckinDuration()
  {
    $element = new Zend_Form_Element_Text('checkinDuration');
    $validator = new Api_Checkin_DurationValidator();
    $element->addValidator($validator)
      ->setRequired();

    return $element;
  }
}