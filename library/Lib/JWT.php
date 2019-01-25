<?php

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

class Lib_JWT
{
  const USER_ID = 'uid';
  const GUEST_ID = '0230ec1d-dc7b-42e6-89d3-3707ee5ade71';

  static public function getHeaderMatches($request) {
    preg_match(
      '/Bearer ([a-z0-9\.\-_]*)/i',
      $request->getHeader('Authorization', ''),
      $matches);
    return $matches;
  }

  /**
   * Parses and accepts or rejects the JWT from the request header.
   * @param $request Zend_Controller_Request_Abstract
   * @param $secret string
   */
  static public function setup(Zend_Controller_Request_Abstract $request, $secret)
  {
    $matches = self::getHeaderMatches($request);
    switch(sizeof($matches)) {
      case 0:
        Globals::clearJWT();
        // User id of the unlogged user:
        return self::GUEST_ID;
        break;
      case 2:
        $token = self::getParsedToken($matches[1], $secret);
        if (Lib_JWT_Blacklist::isBlacklisted($token->__toString())) {
          throw new Lib_JWT_Exception(Lib_JWT_Exception::ERROR_TOKEN_BLACKLISTED);
        }
        Globals::setJWT($token->__toString());
        return $token->getClaim(self::USER_ID);
        break;
      default:
        throw new Exception(Lib_JWT_Exception::ERROR_CANNOT_PARSE_HEADER);
        break;
    }
  }

  static public function getParsedToken($tokenAsString, $secret) {
    try {
      $token = (new Parser())->parse((string) $tokenAsString);
    } catch (Exception $e) {
      throw new Lib_JWT_Exception(Lib_JWT_Exception::ERROR_CANNOT_PARSE_TOKEN);
    }
    $timestamp = Utils::date('timestamp');
    if (APPLICATION_ENV === 'test') {
      $datetime = Utils::date('Y-m-d H:i:s');
      Globals::getLogger()->test("It's now $datetime $timestamp");
    }
    $now = new DateTime();
    $now->setTimestamp($timestamp);

    if ($token->isExpired($now)) {
      throw new Lib_JWT_Exception(Lib_JWT_Exception::ERROR_TOKEN_EXPIRED);
    }
    $signer = new Sha256();
    if (!$token->verify($signer, $secret)) {
      throw new Lib_JWT_Exception(Lib_JWT_Exception::ERROR_TOKEN_INVALID);
    }
    return $token;
  }

  static public function create($secret, $time, $userId)
  {
    $signer = new Sha256();
    $token = (new Builder())
      ->setExpiration($time + JWT_TTL)
      ->set(self::USER_ID, $userId)
      ->sign($signer, $secret)
      ->getToken();
    return $token->__toString();
  }

  /**
   * Whether the token is valid and not expired yet.
   * @return boolean
   */
  static public function isBlacklistable($tokenAsString, $secret) {
    try {
      self::getParsedToken($tokenAsString, $secret);
      return true;
    } catch (Lib_JWT_Exception $e) {
      return false;
    } catch (Exception $e) {
      return true;
    }
  }
}