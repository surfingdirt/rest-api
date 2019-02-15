<?php

class Api_Media_Photo_Row extends Media_Item_Photo_Row
{
  protected $_formClass = 'Api_Media_Form_Photo';
  
  public function __construct($config = array())
  {
    parent::__construct($config);

    $this->mediaType = Media_Item::TYPE_PHOTO;
  }

  protected function _postDelete()
  {
    Api_Image::cleanupById($this->storageType, $this->imageId);
    parent::_postDelete();
  }
}