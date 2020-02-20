<?php

class Data_Form_Element_Bio extends Lib_Form_Element_Translated
{
  public function __construct($form, $options = null, $required = false)
  {
    parent::__construct('bio', $options, $required);
  }
}