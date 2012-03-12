<?php
$t1 = microtime(true);

define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH . '/application');

// Include path
set_include_path(
    BASE_PATH . '/library'
    . PATH_SEPARATOR . BASE_PATH . '/models'
    . PATH_SEPARATOR . BASE_PATH . '/library/HTMLPurifier'
    . PATH_SEPARATOR . BASE_PATH . '/library/sphinx'
);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : 'development'));

if(APPLICATION_ENV != 'production') {

	//error_log("                                                                                                                                                                                                                                                ");
	//error_log(var_export(apache_request_headers(), TRUE));
	//error_log('POST '.var_export($_POST, TRUE));
	//error_log('FILES '.var_export($_FILES, TRUE));
	
}

// Zend_Application
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
ini_set('unserialize_callback_func', 'Zend_Loader::loadClass');

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
$env = APPLICATION_ENV;

$constants = $config->$env->constants;
foreach($constants as $name => $value){
	$constantName = strtoupper($name);
	defined($constantName) || define($constantName, $value);
}

defined("CURRENT_DIR") || define("CURRENT_DIR", getcwd());

ini_set('upload_max_filesize', GLOBAL_UPLOAD_MAXSIZE);
ini_set('upload_tmp_dir', GLOBAL_UPLOAD_TMPDIR);

date_default_timezone_set('UTC');

ini_set('session.name', 'sId');

//Flash session ids:
$sessionId = null;
if (isset($_POST[session_name()]) && !empty($_POST[session_name()])) {
	Zend_Session::setId($_POST[session_name()]);
	$sessionId = $_POST[session_name()];
} elseif (isset($_GET[session_name()]) && !empty($_GET[session_name()])){
	Zend_Session::setId($_GET[session_name()]);
	$sessionId = $_GET[session_name()];
}
if($sessionId) {
	setcookie(session_name(), $sessionId);
}

Zend_Session::start(array('cookie_domain' => COOKIE_DOMAIN));


$frontController = Zend_Controller_Front::getInstance();
$frontController->setRequest('Lib_Controller_Request');
$frontController->setControllerDirectory(array('default' => APPLICATION_PATH.'/controllers'));

$dummyRoute = new Zend_Controller_Router_Route('/dummy/');
$router = $frontController->getRouter();
Globals::setRouter($router);

$router->addRoutes(array(
	'default' => new Zend_Rest_Route($frontController),
	'test' => new Zend_Controller_Router_Route('test/:action', array('controller' => 'test', 'action' => 'freeze-timer')),
	
	'riderConfirmation' => new Zend_Controller_Router_Route('/riders/:id/confirmation/', array('controller' => 'custom', 'action' => 'rider-confirmation')),
	'lostPassword' => new Zend_Controller_Router_Route('/lost-password/', array('controller' => 'custom', 'action' => 'lost-password')),
	'activateNewPassword' => new Zend_Controller_Router_Route('/riders/:id/activate-new-password/', array('controller' => 'custom', 'action' => 'activate-new-password')),
	
	'userAlbums' => new Zend_Controller_Router_Route('/riders/:id/albums/', array('controller' => 'riders-albums', 'action' => 'list')),
	'comments' => new Zend_Controller_Router_Route('/:itemType/:itemId/comments/', array('controller' => 'comments')),
	'countryRegions' => new Zend_Controller_Router_Route('/countries/:countryId/regions/', array('controller' => 'regions', 'action' => 'list')),
	
	'countryLocations' => new Zend_Controller_Router_Route('/countries/:countryId/locations/', array('controller' => 'locations', 'action' => 'list')),
	'regionLocations' => new Zend_Controller_Router_Route('/regions/:regionId/locations/', array('controller' => 'locations', 'action' => 'list')),
	
	'checkinsBySpot' => new Zend_Controller_Router_Route('/checkins/spots/:spotId/', array('controller' => 'checkins', 'action' => 'list')),
	'checkinsBySpotCurrent' => new Zend_Controller_Router_Route('/checkins/spot/:spotId/current/', array('controller' => 'checkins', 'action' => 'list', 'onlyCurrent' => true)),
	'checkinsByCountry' => new Zend_Controller_Router_Route('/checkins/countries/:countryId/', array('controller' => 'checkins', 'action' => 'list')),
	'checkinsByRegion' => new Zend_Controller_Router_Route('/checkins/regions/:regionId/', array('controller' => 'checkins', 'action' => 'list')),
	'checkinsByRider' => new Zend_Controller_Router_Route('/checkins/riders/:riderId/', array('controller' => 'checkins', 'action' => 'list')),
	'checkinsByRiderCurrent' => new Zend_Controller_Router_Route('/checkins/riders/:riderId/current/', array('controller' => 'checkins', 'action' => 'list', 'onlyCurrent' => true)),
	'checkinsAroundSpot' => new Zend_Controller_Router_Route('/checkins/spots/:spotId/around/', array('controller' => 'checkins', 'action' => 'list', 'fetchAround' => true)),
	'checkinsAroundLocation' => new Zend_Controller_Router_Route('/checkins/around/', array('controller' => 'checkins', 'action' => 'list', 'fetchAround' => true)),

	'createdata' => $dummyRoute,
	'editdata' => $dummyRoute,
	'deletedata' => $dummyRoute,
	'userregister' => $dummyRoute,
	'uploadvideo' => $dummyRoute,
	'postcomment' => $dummyRoute,
	'userupdate' => $dummyRoute,
	'createalbum' => $dummyRoute,
	'editalbum' => $dummyRoute,
	'editcomment' => $dummyRoute,
	'uploadphotomain' => $dummyRoute,
	'editvideo' => $dummyRoute,
));

$frontController->registerPlugin(new Zend_Controller_Plugin_PutHandler())
				->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array('controller' => 'error','action' => 'exception')));
// HELPERS
Zend_Controller_Action_HelperBroker::addPrefix('Lib_Controller_Helper');
Zend_Controller_Action_HelperBroker::addPath('../library/Lib/Controller/Helper', 'Lib_Controller_Helper');

error_reporting(E_ALL|E_STRICT);

$db = Globals::getMainDatabase();
Zend_Db_Table::setDefaultAdapter($db);
if(ALLOW_CACHE){
	Zend_Db_Table::setDefaultMetadataCache(Globals::getGlobalCache());
}

$frontController->dispatch();

$t2 = microtime(true); 
$time = ($t2 - $t1) *1000;
error_log("Request took $time milliseconds");
