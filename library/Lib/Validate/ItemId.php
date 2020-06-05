<?php

class Lib_Validate_ItemId extends Lib_Validate_Data
{
  const DOES_NOT_EXIST = 'itemIdDoesNotExist';
  const EXISTS = 'itemIdExists';

  /**
   * @var array
   */
  protected $_messageTemplates = array(
    self::DOES_NOT_EXIST => "itemIdDoesNotExist",
    self::EXISTS => "itemIdExists"
  );

  public function __construct($constraint)
  {
    $this->_constraint = $constraint;
  }

  public function isValid($value)
  {
    $args = func_get_args();

    // In order to circumvent the limitation in argument numbers imposed
    // by Zend_Validate_Interface, we have to sneak a 2nd argument:
    $context = isset($args[1]) ? $args[1] : array();
    $this->_context = $context;
    $found = $this->_findData($value);

    if ($this->_constraint == self::MUST_EXIST) {
      // Must exist
      if ($found) {
        return true;
      } else {
        if ($this->_emptyAllowed && empty($value)) {
          return true;
        } else {
          $this->_error(self::DOES_NOT_EXIST);
          return false;
        }
      }
    } else {
      // Must not exist
      if (!$found) {
        return true;
      } else {
        $this->_error(self::EXISTS);
        return false;
      }
    }
  }

  protected function _findData($value, $returnValue = false)
  {
    $itemId = $value;
    $itemType = $this->_context['itemType'];
    $resources = Api::getReactionResources();
    $table = new $resources[$itemType]();

    try {
      $where = $table->getAdapter()->quoteInto('id = ?', $itemId);
      $where .= " AND status > 0";
      $result = $table->fetchRow($where);
    } catch (Exception $e) {
      $logMessage = "Type: " . get_class($e) . PHP_EOL;
      $logMessage .= "Code: " . $e->getCode() . PHP_EOL;
      $logMessage .= "Message: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString();

      Globals::getLogger()->error($logMessage);
    }
    return !empty($result);
  }
}