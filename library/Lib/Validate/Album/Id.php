<?php

class Lib_Validate_Album_Id extends Lib_Validate_Album
{
  protected $_album;

  public function isValid($value, $context = array())
  {
    if (!parent::isValid($value)) {
      return false;
    }

    if (!$this->_album->isEditableBy(Globals::getUser(), Globals::getAcl())) {
      $this->_error(parent::ALBUMNOTWRITABLE);
      return false;
    }

    return true;
  }

  protected function _findData($value, $returnValue = false)
  {
    $result = null;
    try {
      $result = $this->_album = Media_Album_Factory::buildAlbumById($value);
      $returnValue = ($result->status == Data::VALID);
    } catch (Exception $e) {
      $logMessage = "Type: " . get_class($e) . PHP_EOL;
      $logMessage .= "Code: " . $e->getCode() . PHP_EOL;
      $logMessage .= "Message: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString();

      Globals::getLogger()->error($logMessage, Zend_Log::ERR);
    }

    if ($returnValue) {
      return $result;
    }

    return !empty($result);
  }
}