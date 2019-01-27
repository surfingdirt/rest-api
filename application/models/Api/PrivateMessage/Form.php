<?php

class Api_PrivateMessage_Form extends PrivateMessage_Form
{
  /**
   * Factory for 'to' element: a user that must exist
   *
   * @return Lib_Form_Element_Username
   */
  protected function getToUser()
  {
    $element = new Lib_Form_Element_UserId('toUser', true, true);
    $element->setRequired(true);
    return $element;
  }
}