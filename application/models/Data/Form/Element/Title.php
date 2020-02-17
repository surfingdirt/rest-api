<?php

class Data_Form_Element_Title extends Lib_Form_Element_Translated
{
  public function __construct($form, $options = null, $required = true)
  {
    parent::__construct('title', $options, $required);
  }
}