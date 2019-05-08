<?php

class Api_ErrorCodes
{
  /*
   * IMAGES
   */
  const IMAGE_BAD_STORAGE_TYPE = 10001;
  const IMAGE_UPLOAD_FAILED = 10002;
  const IMAGE_STORAGE_METHOD_NOT_IMPLEMENTED = 10003;
  const IMAGE_FOLDER_CREATION_FAILED = 10004;
  const IMAGE_NO_EXTENSION = 10005;
  const IMAGE_COULD_NOT_MOVE_UPLOADED_FILE = 10006;
  const IMAGE_CLEANUP_METHOD_NOT_IMPLEMENTED = 10007;
  const IMAGE_DB_SAVE_FAILURE = 10008;
  const IMAGE_DB_DELETE_FAILURE = 10009;
  const IMAGE_NO_FILE = 10010;
  const IMAGE_BAD_MIME = 10011;

  /*
   * MEDIA
   */
  const MEDIA_BAD_MEDIA_TYPE = 11001;
  const MEDIA_BAD_ALBUM_FOR_POST = 11002;
  const MEDIA_BAD_MEDIA_SUBTYPE = 11003;

  /*
   * ALBUMS
   */
  const STATIC_ALBUM_NOT_EMPTY = 12001;
  const NON_STATIC_ALBUM_CANNOT_BE_DELETED = 12002;

  /*
   * NOT FOUND
   */
  const RESOURCE_NOT_FOUND = 13001;
  const ROUTING_NOT_FOUND = 13002;

  /*
   * TOKEN CONTROLLER
   */
  const TOKEN_MISSING_VALUE = 14001;
  const TOKEN_FAILED_TO_LOGIN = 14002;
  const TOKEN_LOGIN_SYSTEM_ERROR = 14003;
  const TOKEN_LOGOUT_SYSTEM_ERROR = 14004;
  const TOKEN_EXISTING = 14005;

  /*
   * FORBIDDEN
   */
  const FORBIDDEN_BAD_TOKEN = 15001;
  const FORBIDDEN_RESTRICTED_ACCESS = 15002;

  /*
   * FORMS
   */
  const FORM_BAD_INPUT = 16001;

  /*
   * GENERAL
   */
  const CONNECTION_REFUSED = 17001;


}