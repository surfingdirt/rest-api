<?php

class Api_Media_Video_Row extends Media_Item_Video_Row
{
  protected $_formClass = 'Api_Media_Form_Video';

  public function __construct($config = array())
  {
    parent::__construct($config);

    $this->mediaType = Media_Item::TYPE_VIDEO;
  }
}