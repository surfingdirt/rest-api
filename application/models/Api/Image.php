<?php

class Api_Image extends Api_Data
{
  const IMAGE_TYPE_PLAIN = 0;
  const IMAGE_TYPE_THUMB = 1;

  protected $_name = Constants_TableNames::IMAGE;

  protected $_rowClass = 'Api_Image_Row';
  protected $_rowsetClass = 'Api_Image_Rowset';

  public function getItemType()
  {
    return 'image';
  }
}