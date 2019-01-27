<?php

class Api_PrivateMessage_Row extends PrivateMessage_Row
{
  protected $_formClass = 'Api_PrivateMessage_Form';

  /**
   * This array contains the list of attributes that
   * should not be sent to a client.
   * @var array
   */
  protected $_hiddenFromClientAttr = array();

  /**
   * This method returns the data that is 'public',
   * that is to say, visible to the client.
   */
  public function getDataForClient()
  {
    $ret = array();
    foreach ($this->_data as $name => $value) {
      if (in_array($name, $this->_hiddenFromClientAttr)) {
        continue;
      }

      $ret[$name] = $value;
    }
    return $ret;
  }
}