<?php

class ErrorController extends Zend_Controller_Action
{
  public function exceptionAction()
  {
    $errors = $this->_getParam('error_handler');
    $e = $errors->exception;
    $class = get_class($e);
    $logMessage = "Type: " . $errors->type . ' - ' . get_class($e) . PHP_EOL;
    $logMessage .= "Code: " . $e->getCode() . PHP_EOL;
    $logMessage .= "Message: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString();

    $this->getResponse()->clearBody();
    $this->getResponse()->setRawHeader('Content-Type: application/json; charset=UTF-8');
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setViewScriptPathSpec('view.phtml');

    if (in_array($errors->type, array(
      Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER,
      Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION))) {
      return $this->_routingNotFound($e);
    }

    switch ($class) {
      case 'Api_Exception_BadRequest':
        return $this->_badRequest($e);

      case 'Lib_JWT_Exception':
      case 'Api_Exception_Unauthorised':
        return $this->_forbidden($e);

      case 'Api_Exception_NotFound':
        return $this->_resourceNotFound($e);

      default:
        Globals::getLogger()->exception($logMessage);
        return $this->_internalServerError($e);
    }
  }

  protected function _notFound($e, $code) {
    $this->_errorHeader('HTTP/1.1 404 Not Found');
    if (APPLICATION_ENV == "test" || APPLICATION_ENV == 'development') {
      $error = array(
        'type' => get_class($e),
        'code' => $code,
        'trace' => $e->getTrace(),
        'message' => $e->getMessage()
      );
    } else {
      $error = array('code' => $code);
    }
    $this->view->output = array('errors' => array('topLevelError' => $error));
  }

  protected function _routingNotFound($e)
  {
    $this->_notFound($e,Api_ErrorCodes::ROUTING_NOT_FOUND);
  }

  protected function _resourceNotFound($e)
  {
    $this->_notFound($e, Api_ErrorCodes::RESOURCE_NOT_FOUND);
  }

  protected function _forbidden($e)
  {
    $this->_errorHeader('HTTP/1.1 403 Forbidden');
    $this->_errorBody($e);
  }

  protected function _badRequest($e)
  {
    $this->_errorHeader('HTTP/1.1 400 Bad Request');
    $this->_errorBody($e);
  }

  protected function _internalServerError($e)
  {
    $this->_errorHeader('HTTP/1.1 500 Internal Server Error');
    $this->_errorBody($e);
  }

  protected function _errorHeader($rawHeader)
  {
    $this->getResponse()->setRawHeader($rawHeader);
  }

  protected function _errorBody($e) {
    $error = array(
      'code' => $e->getCode(),
      'message' => $e->getMessage(),
    );
    if (APPLICATION_ENV == "test" || APPLICATION_ENV == 'development') {
      $error['type'] = get_class($e);
      $error['trace'] = $e->getTrace();
    }
    $this->view->output = array('errors' => array('topLevelError' => $error));
  }
}