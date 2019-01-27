<?php

class Api_Image extends Api_Data
{
  protected $_name = Constants_TableNames::IMAGE;

  protected $_rowClass = 'Api_Image_Row';
  protected $_rowsetClass = 'Api_Image_Rowset';

  public function getItemType()
  {
    return 'image';
  }
}