<?php
class User_Form_OAuth_Register extends Lib_Form
{
  public function __construct()
  {
    $username = new Lib_Form_Element_Username(null, false, false, true);
    $username->setRequired()->addValidator('NotEmpty', true);
    $email = new Lib_Form_Element_Email(true);
    $timezone = new Lib_Form_Element_Timezone(null);
    $locale = new Lib_Form_Element_Locale(null);

    $this->addElements(array(
      $username,
      $email,
      $timezone,
      $locale,
    ));
  }
}