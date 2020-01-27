<?php
/*
 * APPLICATION ENVIRONMENT
 */
define('PRODUCTION', 'production');
define('DEVELOPMENT', 'development');
define('TEST', 'test');

defined('APPLICATION_ENV')
|| define('APPLICATION_ENV',
  (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
    : PRODUCTION));

/*
 * PATHS
 */
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH . '/application');
defined("CURRENT_DIR") || define("CURRENT_DIR", getcwd());

set_include_path(
  BASE_PATH . '/library'
  . PATH_SEPARATOR . BASE_PATH . '/application/'
  . PATH_SEPARATOR . BASE_PATH . '/application/controllers'
  . PATH_SEPARATOR . BASE_PATH . '/application/models'
);

require BASE_PATH . '/library/vendor/autoload.php';

/*
 * FILE LOADING
 */
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
ini_set('unserialize_callback_func', 'Zend_Loader::loadClass');


/*
 * CONSTANTS
 */
$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
$env = APPLICATION_ENV;
$constants = $config->$env->constants;
foreach ($constants as $name => $value) {
  $constantName = strtoupper($name);
  defined($constantName) || define($constantName, $value);
}


/*
 * PHP SETTINGS
 */
ini_set('upload_max_filesize', GLOBAL_UPLOAD_MAXSIZE);
ini_set('upload_tmp_dir', GLOBAL_UPLOAD_TMPDIR);

ini_set('display_errors', $config->$env->phpSettings->display_errors);
ini_set('display_startup_errors', $config->$env->phpSettings->display_startup_errors);
ini_set('error_log', $config->$env->phpSettings->error_log);
ini_set('html_errors', $config->$env->phpSettings->html_errors);
ini_set('log_errors', $config->$env->phpSettings->log_errors);
date_default_timezone_set('UTC');
error_reporting(E_ALL | E_STRICT);


/*
 * ZEND CONTROLLER SETUP
 */
$frontController = Zend_Controller_Front::getInstance();
$frontController
  ->setRequest('Lib_Controller_Request')
  ->setControllerDirectory(array('default' => APPLICATION_PATH . '/controllers'))
  ->registerPlugin(new Lib_Controller_Plugin_RequestHandler())
  ->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(
      array('controller' => 'error', 'action' => 'exception')));
Zend_Controller_Action_HelperBroker::addPrefix('Lib_Controller_Helper');
Zend_Controller_Action_HelperBroker::addPath('../library/Lib/Controller/Helper', 'Lib_Controller_Helper');

/*
 * ROUTES
 */
Routes::install($frontController);

/*
 * DATABASE
 */
$db = Globals::getMainDatabase();
Zend_Db_Table::setDefaultAdapter($db);
if (ALLOW_CACHE) {
  Zend_Db_Table::setDefaultMetadataCache(Globals::getGlobalCache());
}

if (APPLICATION_ENV === TEST) {
  Globals::getLogger()->test("\n");
}

/*
 * OPENTRACING
 */
if (OPENTRACING_ENABLED) {
  $tracer = Globals::getTracer();
  $span = $tracer->startTrace();
  $span->setName("API request handling for ".$_SERVER['REQUEST_URI']);
  $span->start();
}

/*
 * HANDLE THE REQUEST
 */
if (APPLICATION_ENV === TEST) {
  // Log auth header
}

$cleanCache = isset($_GET['cleanCache']) && isset($_GET['cleanCacheKey']) && $_GET['cleanCacheKey'] === CLEAN_CACHE_KEY;
if ($cleanCache) {
  Globals::getGlobalCache()->clean();
}

$frontController->dispatch();

if (OPENTRACING_ENABLED) {
  $span->finish();
  $tracer->flush();
}
