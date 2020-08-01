<?php
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use Kreait\Firebase\JWT\IdTokenVerifier;

class Lib_Firebase
{
  private static function _getOAuthTokenEmail() {
    if (APPLICATION_ENV !== 'test') {
      return false;
    }
    $headers = apache_request_headers();
    if(!isset($headers['X-oAuthTokenEmail'])) {
      return false;
    }

    return $headers['X-oAuthTokenEmail'];
  }

  public static function getUserDataFromToken($tokenAsObject)
  {
    if ($email = self::_getOAuthTokenEmail()) {
      // Test mode
      return array('email' => $email);
    }

    $data = $tokenAsObject->payload();
    return array('email' => $data['email']);
  }

  public static function getVerifiedToken($projectId, $tokenAsString)
  {
    if (self::_getOAuthTokenEmail()) {
      return new stdClass();
    }

    $verifier = IdTokenVerifier::createWithProjectId($projectId);
    try {
      $tokenAsObject = $verifier->verifyIdToken($tokenAsString);
    } catch (IdTokenVerificationFailed $e) {
      Globals::getLogger()->error("Invalid Firebase token: ". $e->getMessage());
      return null;
    }

    return $tokenAsObject;
  }
}