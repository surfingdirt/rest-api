<?php

/**
 * Class used to retrieve application-wide objects and data (registry)
 */
class Globals
{
  /**
   * Database object (singleton)
   * @var Zend_Db_Adapter
   */
  private static $_db;
  /**
   *
   */
  private static $_routeConfig;
  /**
   * Routing configuration (singleton)
   * @var Zend_Controller_Router_Rewrite
   */
  private static $_router;
  /**
   * User object
   *
   * @var User_Row
   */
  private static $_user;
  /**
   * Acl
   *
   * @var Lib_Acl
   */
  private static $_acl;
  /**
   * Login error data for display
   *
   * @var array
   */
  private static $_loginErrorData = array();
  /**
   * Logger object (singleton)
   *
   * @var Zend_Log
   */
  private static $_logger;
  /**
   * Cache object (singleton)
   *
   * @var Zend_Log
   */
  private static $_globalCache;
  /**
   * Global JWT object
   *
   * @var string
   */
  private static $_JWT;
  /**
   * Global Translate object
   *
   * @var Zend_Translate
   */
  private static $_translate;
  /**
   * Application name
   *
   * @var string
   */
  private static $_app;
  /**
   * List of allowed file extensions for upload
   *
   * @var array
   */
  private static $_filesExtensionUploadWhiteList = array(
    'jpg', 'jpeg', 'gif', 'png', 'swf', 'pdf', 'ppt', 'doc', 'xls', 'txt'
  );

  private static $_tracer;

  private static $_JWTBlacklist;

  /**
   * Returns a database connection
   *
   * @return Zend_Db_Adapter_Abstract
   */
  public static function getMainDatabase()
  {
    if (empty(self::$_db)) {
      $parameters = array('host' => GLOBAL_DB_HOST,
        'username' => GLOBAL_DB_USER,
        'password' => GLOBAL_DB_PASSWORD,
        'dbname' => GLOBAL_DB_NAME,
        'charset' => 'utf8mb4'
      );
      $db = Zend_Db::factory('Pdo_Mysql', $parameters);
      $db->getConnection();

      self::$_db = $db;
    }
    return (self::$_db);
  }

  /**
   * Build and save in cache the route configuration,
   * from the specified file and format.
   *
   * @param string $app
   * @param string $file
   * @param string $format
   * @throws Lib_Exception
   * @return Zend_Config
   */
  public static function getRouteConfig($file, $format)
  {
    switch ($format) {
      case 'xml':
        $content = new Zend_Config_Xml($file);
        break;
      case 'ini':
        $content = new Zend_Config_Ini($file);
        break;
      default:
        throw new Lib_Exception('Route config format not supported: ' . $format);
    }
    return $content;
  }

  public static function setRouter($router)
  {
    self::$_router = $router;
  }

  /**
   * Return the router object for the current module
   *
   * @param string $app
   * @throws Lib_Exception
   * @return Zend_Controller_Router_Rewrite
   */
  public static function getRouter($app = null)
  {
    return self::$_router;
  }

  /**
   * Return the User object
   *
   * @return User
   * @throws Lib_Exception_User
   */
  public static function getUser()
  {
    return self::$_user;
  }

  /**
   * Sets the User objectt
   *
   * @param User_Row $user
   */
  public static function setUser($user)
  {
    self::$_user = $user;
  }

  public static function getAcl()
  {
    if (empty(self::$_acl)) {
      $user = self::getUser();
      self::$_acl = new Lib_Acl($user);
    }
    return self::$_acl;
  }

  /**
   * Sets whether to use zlib compression
   *
   * @param boolean $boolean
   */
  public static function setCompression($boolean)
  {
    ini_set('zlib.output_compression', $boolean);
  }

  public static function setLoginErrorData($arr)
  {
    self::$_loginErrorData = $arr;
  }

  public static function getLoginErrorData()
  {
    return self::$_loginErrorData;
  }

  /**
   * Configure and return the logger instance
   *
   * @return Zend_Log
   */
  public static function getLogger()
  {
    if (empty(self::$_logger)) {
      if (!empty(self::$_user)) {
        $userId = self::$_user->{User::COLUMN_USERID};
      } else {
        $userId = 'null';
      }

      $logger = new Logger($userId);
      self::$_logger = $logger;
    }

    return self::$_logger;
  }

  private static function _getLogFile()
  {
    $file = APP_DEBUGDIR . date('Y-m-d') . '.log';
    return $file;
  }

  /**
   * Get the global cache instance
   *
   * @return Zend_Cache_Core
   */
  public static function getGlobalCache()
  {
    if (empty(self::$_globalCache)) {
      $cache = Cache::factory(GLOBAL_CACHE_METHOD, array('cache_dir' => GLOBAL_CACHE_FILE_DIR));
      self::$_globalCache = $cache;
    }
    return self::$_globalCache;
  }

  /**
   * Get the global JWT.
   * @return string
   */
  public static function getJWT()
  {
    return self::$_JWT;
  }

  /**
   * Set the global JWT.
   * @param JWT string
   * @throws Error
   */
  public static function setJWT($JWT)
  {
    if (self::$_JWT) {
      throw new Error("JWT already set");
    }

    self::$_JWT = $JWT;
  }

  /**
   * Clears the global JWT.
   */
  public static function clearJWT()
  {
    self::$_JWT = null;
  }

  public static function getJWTBlacklist()
  {
    $cache = self::getGlobalCache();

  }

  /**
   * Get the Zend_Translate instance
   * After object has been initialized, it can
   * be updated by passing a locale string
   *
   * @return Zend_Translate
   */
  public static function getTranslate($updateTo = null)
  {
    if ($updateTo !== null) {
      self::$_translate = Lib_Translate_Factory::build($updateTo);
    } elseif (empty(self::$_translate)) {
      self::$_translate = Lib_Translate_Factory::build();
    }
    return self::$_translate;
  }

  /**
   * Reset all static data
   * Used for unit testing
   */
  public static function resetAll()
  {
    self::$_db = null;
    self::$_router = null;
    self::$_user = null;
    self::$_loginErrorData = null;
    self::$_logger = null;
    self::$_globalCache = null;
  }

  public static function getAppName()
  {
    return self::$_app;
  }

  public static function setAppName($app)
  {
    self::$_app = $app;
  }


  /**
   * Get the chosen language from subdomain
   *
   * @return string|null
   */
  public static function getSubdomainLanguage()
  {
    //return 'fr';

    if (!isset($_SERVER['HTTP_HOST'])) {
      throw new Lib_Exception('No info found for translation');
    }

    $domain = $_SERVER['HTTP_HOST'];
    $regex = '/^([a-zA-Z0-9-]+)\.' . GLOBAL_DOMAIN . '\.' . GLOBAL_EXTENSION . '$/';
    $matches = null;
    preg_match($regex, $domain, $matches);

    // Do not return www for example:
    if (isset($matches[1]) && $matches[1] != APP_SUBDOMAIN) {
      return $matches[1];
    }

    return null;
  }

  public static function getDefaultSiteLanguage()
  {
    return self::getDefaultSiteLanguage();
  }

  public static function getFileExtensionUploadWhiteList()
  {
    return self::$_filesExtensionUploadWhiteList;
  }

  /**
   * Configure and return the tracer instance
   *
   * @return Tracer
   */
  public static function getTracer()
  {
    if (empty(self::$_tracer)) {
      $tracer = new Tracer();
      self::$_tracer = $tracer;
    }

    return self::$_tracer;
  }

}
