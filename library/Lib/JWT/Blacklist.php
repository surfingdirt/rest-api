<?php

class Lib_JWT_Blacklist
{
  const CACHE_ID = 'JWTBlacklist';

  public static function addToken($token)
  {
    $cache = Globals::getGlobalCache();
    $list = $cache->load(self::CACHE_ID);
    if (!$list) {
      $list = [];
    }
    $list[] = $token;
    $cache->save($list, self::CACHE_ID);
  }

  public static function removeToken($token)
  {
    $cache = Globals::getGlobalCache();
    $list = $cache->load(self::CACHE_ID);
    if (!$list) {
      return;
    }
    unset($list[$token]);
    $cache->save($list, self::CACHE_ID);
  }

  public static function isBlacklisted($tokenAsString)
  {
    $list = Globals::getGlobalCache()->load(self::CACHE_ID);
    if (!$list) {
      return false;
    }
    $isBlacklisted = in_array($tokenAsString, $list) ? "true" : "false";
    Globals::getLogger()->security("$tokenAsString is Blacklisted: $isBlacklisted");
    return in_array($tokenAsString, $list);
  }

  public static function cleanupInvalidAndExpiredTokens($secret)
  {
    $cache = Globals::getGlobalCache();
    $list = $cache->load(self::CACHE_ID);
    if (!$list) {
      $list = [];
    }
    foreach ($list as $tokenAsString) {
      try {
        Lib_JWT::getParsedToken($tokenAsString, $secret);
      } catch (Lib_JWT_Exception $e) {
        // Token is invalid or expired, remove it
        error_log("removing invalid token '$tokenAsString'");
        unset($list[$tokenAsString]);
      }
    }
    $cache->save($list, self::CACHE_ID);
  }
}