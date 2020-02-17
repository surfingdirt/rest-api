<?php

class Lib_Form_Element_Translated extends Zend_Form_Element_Text
{
  public function __construct($spec, $options = null, $required = true)
  {
    parent::__construct($spec, $options);

    $this->setRequired($required)
         //->addPrefixPath('Lib_Validate', 'Lib/Validate', 'Validate')
         ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter');

    $this->addValidator(new Lib_Validate_Translated($required));
  }
}