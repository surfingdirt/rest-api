<?php

class Lib_Form_Element_Timezone extends Zend_Form_Element_Text
{
  protected $_validator = 'Lib_Validate_Timezone';

  protected $_required = true;

  public function __construct()
  {
    parent::__construct('timezone');
  }
}