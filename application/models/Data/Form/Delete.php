<?php

class Data_Form_Delete extends Lib_Form
{
  /**
   * Object to be delete via this form
   *
   * @var Data_Row
   */
  protected $_object;

  public function __construct(Data_Row $object, $options = null)
  {
    $this->_object = $object;
    parent::__construct($options, true);
    $this->setAttrib('id', 'deleteForm');
  }
}