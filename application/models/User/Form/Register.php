<?php

class User_Form_Register extends Lib_Form
{
  public function __construct($options = null)
  {
    parent::__construct($options, false);

    $username = new Lib_Form_Element_Username(null, false, false, true);
    $username->setRequired()->addValidator('NotEmpty', true);

    $password = new Lib_Form_Element_Password(true);
    $passwordConfirm = new Lib_Form_Element_Password_Confirm(true, $this, $password->getName());

    $email = new Lib_Form_Element_Email(true);

    // TODO: handle language!

    $this->addElements(array(
      $username,
      $password,
      $passwordConfirm,
      $email,
    ));
  }

  /**
   * Overload of isValid is necessary in order to set the token for
   * the password confirm 'IsIdentical' token and to choose between
   * login methods
   *
   * @param array $data
   * @return boolean
   */
  public function isValid($data)
  {
    $this->getElement(User::INPUT_PASSWORD)->setRequired(true);
    $this->getElement(User::INPUT_PASSWORD_CONFIRM)->setRequired(true);

    $valid = parent::isValid($data);
    return $valid;
  }
}