<?php

class Api_Reaction_Form extends Data_Form
{
  protected function _setup()
  {
    $elements = array(
      'itemType' => new Api_Reaction_Form_ItemType($this),
      'itemId' => new Api_Reaction_Form_ItemId($this),
      'type' => new Api_Reaction_Form_Type($this),
    );

    $this->addElements($elements);
  }
}