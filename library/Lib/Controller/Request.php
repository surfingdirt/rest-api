<?php

class Lib_Controller_Request extends Zend_Controller_Request_Http
{
  public function isPut()
  {
    $method = $this->getMethod();
    if ('PUT' == $method) {
      return true;
    }

    /*
     * Ugly workaround for browsers doing a POST request
     * while specifying an id, since they don't handle
     * PUT very well.
     */
    if ('POST' == $method && $this->getParam('id', false)) {
      return true;
    }

    return false;
  }
}