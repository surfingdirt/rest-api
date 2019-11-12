<?php

class Data_Form_Element_Title extends Zend_Form_Element_Text
{
  protected $_form;

  public function __construct($form, $options = null, $required = true)
  {
    $this->_form = $form;
    parent::__construct('title', $options);

    $toLowerFilter = new Zend_Filter_StringToLower();
    $toLowerFilter->setEncoding(APP_PAGE_ENCODING);

    $this->setLabel(ucfirst(Globals::getTranslate()->_('title')))
      ->setRequired($required)
      ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter');
  }

  public function getHint()
  {
    return 'titleHint';
  }
}