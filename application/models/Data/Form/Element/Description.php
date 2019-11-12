<?php

class Data_Form_Element_Description extends Lib_Form_Element_TinyMce
{
  protected $_form;

  protected $_isAdvancedByDefault = false;

  public function __construct($form, $options = null, $required = true)
  {
    $this->_form = $form;

    parent::__construct('description', $options);
    $this->setLabel(ucfirst(Globals::getTranslate()->_('description')))
      ->setRequired($required)
      ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter');
  }

  public function getHint()
  {
    return 'descriptionHint';
  }

}