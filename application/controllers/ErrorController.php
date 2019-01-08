<?php

class ErrorController extends Zend_Controller_Action
{
  public function exceptionAction()
  {
    $view = 'exception';
    $errors = $this->_getParam('error_handler');
    $e = $errors->exception;
    $logMessage = "Type: " . $errors->type . ' - ' . get_class($e) . PHP_EOL;
    $logMessage .= "Code: " . $e->getCode() . PHP_EOL;
    $logMessage .= "Message: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString();

    $this->getResponse()->clearBody();
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setNoRender();

    switch ($errors->type) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        return $this->_notFound();
      default:
        $class = get_class($e);
        switch ($class) {
          case 'Api_Exception_BadRequest':
            return $this->_badRequest();
          case 'Api_Exception_Unauthorised':
            return $this->_forbidden();
          case 'Api_Exception_NotFound':
            return $this->_notFound();
          default:
            error_log($logMessage);
            return $this->_error($e->getMessage());
        }
    }
  }

  protected function _notFound()
  {
    $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
  }

  protected function _forbidden($errorId = 0)
  {
    $this->getResponse()->setRawHeader('HTTP/1.1 403 Forbidden');
  }

  protected function _badRequest($errorId = 0)
  {
    $this->getResponse()->setRawHeader('HTTP/1.1 400 Bad Request');
  }

  protected function _error($errorId = 0)
  {
    $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error')
    ->setHeader('x-mika', $errorId);
  }
}