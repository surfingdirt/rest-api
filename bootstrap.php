<?php
error_reporting(E_ALL);

define('BASE_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', BASE_PATH . '/application');

// Include path
set_include_path(
  '.'
  . PATH_SEPARATOR . BASE_PATH . '/library'
  . PATH_SEPARATOR . BASE_PATH . '/application/models'
  . PATH_SEPARATOR . BASE_PATH . '/library/sphinx'
  . PATH_SEPARATOR . get_include_path()
);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
ini_set('unserialize_callback_func', 'Zend_Loader::loadClass');

//require_once 'PHPUnit/Autoload.php';

// Define application environment
define('APPLICATION_ENV', 'testing');

$_SERVER['SERVER_NAME'] = 'http://test';

$loader = require 'library/vendor/autoload.php';

