<?php

class Api_Reaction_Form_ItemType extends Zend_Form_Element_Select
{
  public $name = 'itemType';

  public function __construct($options = null)
  {
    parent::__construct($this->name, $options);

    $this->setMultiOptions(Api_Reaction::$itemTypes);
  }
}