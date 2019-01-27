<?php

class Api_Album extends Media_Album_Simple
{
  protected $_rowClass = 'Api_Album_Row';
  protected $_rowsetClass = 'Api_Album_Rowset';
}