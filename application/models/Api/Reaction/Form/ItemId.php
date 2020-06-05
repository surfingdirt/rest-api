<?php

class Api_Reaction_Form_ItemId extends Zend_Form_Element
{
  public $name = 'itemId';

  public function __construct($options = null)
  {
    parent::__construct($this->name, $options);
    $this->setRequired(true);
    $this->addValidator(new Lib_Validate_ItemId(Lib_Validate_Data::MUST_EXIST));
  }
}