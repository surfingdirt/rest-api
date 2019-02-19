<?php

class Media_Album_Form extends Data_Form
{
  public function getDescription()
  {
    $element = new Data_Form_Element_Description($this, null, false);
    return $element;
  }
}