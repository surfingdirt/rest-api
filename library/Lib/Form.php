<?php

/**
 * Custom form class that uses its own error rendering and display tags
 *
 */
class Lib_Form extends Zend_Form
{
  protected $_groupClass = 'element-group';

  protected $_submitGroupClass = 'submit-group';

  /**
   * Constructor
   *
   */
  public function __construct($options = null, $csrfProtection = false)
  {
    parent::__construct($options);
    $this->setName('form1');
    $this->addPrefixPath('Lib_Form_Decorator', 'Lib/Form/Decorator', 'Decorator');
    $this->addPrefixPath('Lib_Filter', 'Lib/Filter');
    $this->setTranslator(Globals::getTranslate());

    if ($csrfProtection) {
      $csrfProtection = new Lib_Form_Element_Hash('token');
      $this->addElement($csrfProtection);
    }
  }

  /**
   * Call special formatting functions before populating
   * form with data form database
   *
   * @param array $data
   */
  public function populateFromDatabaseData(array $data)
  {
    $formattedData = $data;
    $elements = $this->getElements();
    foreach ($elements as $name => $element) {
      if (method_exists($element, 'getValueFromDatabase')) {
        $rawValue = isset($data[$name]) ? $data[$name] : null;
        $formattedData[$name] = $element->getValueFromDatabase($rawValue);
      }
    }
    $this->populate($formattedData);
    return $formattedData;
  }

  /**
   * Call special formatting function before storing data
   * into database
   *
   * @param array $data
   * @return array
   */
  public function getFormattedValuesForDatabase()
  {
    $formattedData = $this->getValues();

    foreach ($this->getElements() as $name => $element) {
      $value = $element->getValue();
      if (method_exists($element, 'getFormattedValueForDatabase')) {
        $formattedData[$name] = $element->getFormattedValueForDatabase($value);
      }
    }
    return $formattedData;
  }


  public function getNonEmptyErrors()
  {
    $errors = array();
    $rawErrors = $this->getErrors();
    foreach ($rawErrors as $name => $err) {
      if (!empty($err)) {
        $errors[$name] = $err;
      }
    }
    return $errors;
  }
}