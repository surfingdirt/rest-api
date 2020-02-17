<?php

class Data_Form_Element_Description extends Lib_Form_Element_Translated
{
  public function __construct($form, $options = null, $required = false)
  {
    parent::__construct('description', $options, $required);
  }
}