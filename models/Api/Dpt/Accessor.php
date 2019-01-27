<?php

class Api_Dpt_Accessor extends Api_Data_Accessor
{
  public $publicReadAttributes = array(
    'id',
    'title',
    'simpleTitle',
    'prefix',
    'status',
    'country',
    'bounds',
    'code',
  );
}