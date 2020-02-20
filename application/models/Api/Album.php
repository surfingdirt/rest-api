<?php

class Api_Album extends Media_Album_Simple
{
  const ALBUM_LIST_CACHE_ID = 'albumList';

  protected $_rowClass = 'Api_Album_Row';
  protected $_rowsetClass = 'Api_Album_Rowset';
}