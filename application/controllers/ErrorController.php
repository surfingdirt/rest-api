<?php

class ErrorController extends Zend_Controller_Action
{
  public function exceptionAction()
  {
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
            $this->getResponse()->setRawHeader('Content-Type: application/json; charset=UTF-8');
            $viewRenderer->setViewScriptPathSpec('view.phtml');
            $viewRenderer->setNoRender(false);
            if (APPLICATION_ENV == "test" || APPLICATION_ENV == 'development') {
              $this->view->output = array('error' => $e->getCode(), 'message' => $e->getMessage());
            } else {
              $this->view->output = array('error' => $e->getCode());
            }
            $this->_badRequest();
            break;
          case 'Lib_JWT_Exception':
          case 'Api_Exception_Unauthorised':
            return $this->_forbidden();
          case 'Api_Exception_NotFound':
            return $this->_notFound();
          default:
            Globals::getLogger()->exception($logMessage);

            if (APPLICATION_ENV == "test" || APPLICATION_ENV == 'development') {
              $viewRenderer->setViewScriptPathSpec('view.phtml');
              $viewRenderer->setNoRender(false);
              $this->view->output = array('error' => array(
                'type' => $errors->type . ' - ' . get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
              ));
            } else {
              return $this->_genericError($e->getMessage());
            }
        }
    }
  }

  protected function _notFound()
  {
    $this->_error('HTTP/1.1 404 Not Found');
  }

  protected function _forbidden()
  {
    $this->_error('HTTP/1.1 403 Forbidden');
  }

  protected function _badRequest()
  {
    $this->_error('HTTP/1.1 400 Bad Request');
  }

  protected function _genericError($message)
  {
    $this->_error('HTTP/1.1 500 Internal Server Error');
  }

  protected function _error($rawHeader)
  {
    $this->getResponse()->setRawHeader($rawHeader);
  }
}