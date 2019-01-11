<?php

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

class Lib_JWT
{
  const USER_ID = 'uid';

  /**
   * Parses and accepts or rejects the JWT from the request header.
   * @param $request Zend_Controller_Request_Abstract
   * @param $secret string
   */
  static public function setup(Zend_Controller_Request_Abstract $request, $secret)
  {
    preg_match(
      '/Bearer ([a-z0-9\.\-_]*)/i',
      $request->getHeader('Authorization', ''),
      $matches);

    switch(sizeof($matches)) {
      case 0:
        Globals::clearJWT();
        // User id of the unlogged user:
        return 0;
        break;
      case 2:
        try {
          $token = self::getParsedToken($matches[1]);
        } catch (Exception $e) {
          throw new Lib_JWT_Exception(Lib_JWT_Exception::ERROR_CANNOT_PARSE_TOKEN);
        }
        if ($token->isExpired()) {
          throw new Lib_JWT_Exception(Lib_JWT_Exception::ERROR_TOKEN_EXPIRED);
        }
        $signer = new Sha256();
        if (!$token->verify($signer, $secret)) {
          throw new Lib_JWT_Exception(Lib_JWT_Exception::ERROR_TOKEN_INVALID);
        }
        Globals::setJWT($token->__toString());
        return $token->getClaim(self::USER_ID);
        break;
      default:
        throw new Exception(Lib_JWT_Exception::ERROR_CANNOT_PARSE_HEADER);
        break;
    }

  }

  static public function create($secret, $time, $userId)
  {
    $signer = new Sha256();
    $token = (new Builder())
      ->setExpiration($time + 3600 * 24)
      ->set(self::USER_ID, $userId)
      ->sign($signer, $secret)
      ->getToken()->__toString();
    return $token;
  }

  static public function getParsedToken($tokenString)
  {
    return (new Parser())->parse((string)$tokenString);
  }
}