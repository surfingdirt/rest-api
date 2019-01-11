<?php
class Lib_JWT_Exception extends Exception
{
  const ERROR_CANNOT_PARSE_HEADER = 'cannotParseHeader';
  const ERROR_CANNOT_PARSE_TOKEN = 'cannotParseToken';
  const ERROR_TOKEN_EXPIRED = 'tokenExpired';
  const ERROR_TOKEN_INVALID = 'tokenInvalid';
  const ERROR_TOKEN_BLACKLISTED = 'tokenBlacklisted';
}