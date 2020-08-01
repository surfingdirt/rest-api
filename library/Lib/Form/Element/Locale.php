<?php

class Lib_Form_Element_Locale extends Zend_Form_Element_Text
{
  protected $_validator = 'Lib_Validate_Locale';

  protected $_required = true;

  public function __construct()
  {
    parent::__construct('locale');
  }

}