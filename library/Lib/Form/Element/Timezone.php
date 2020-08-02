<?php

class Lib_Form_Element_Timezone extends Zend_Form_Element_Text
{
  protected $_required = true;

  public function __construct()
  {
    parent::__construct('timezone');

    $this->addValidator(new Lib_Validate_Timezone());
  }
}