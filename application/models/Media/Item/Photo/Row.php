<?php

class Media_Item_Photo_Row extends Media_Item_Row
{
  /**
   * String that appears in urls
   *
   * @var string
   */
  protected $_routeDataType = Constants_DataTypes::PHOTO;

  /**
   * Name of the route used to construct urls
   *
   * @var string
   */
  protected $_route = 'displayphoto';

  /**
   * Name of the route used to construct edition urls
   *
   * @var string
   */
  protected $_editRoute = 'editphoto';

  /**
   * Name of the route used to construct creation urls
   *
   * @var string
   */
  protected $_createRoute = 'uploadphotomain';

  /**
   * Name of the route used to construct delete urls
   *
   * @var string
   */
  protected $_deleteRoute = 'deletephoto';

  /**
   * Name of the class of form used to edit this object
   *
   * @var string
   */
  protected $_formClass = 'Media_Item_Photo_Form';

  public function getMediaType()
  {
    return Media_Item::TYPE_PHOTO;
  }

  public function getMediaSubType()
  {
    if (!in_array($this->mediaSubType, Media_Item_Photo::$allowedMediaSubTypes)) {
      throw new Lib_Exception_Media("Bad subtype: '$this->mediaSubType' for media '$this->id'");
    }
    return $this->mediaSubType;
  }

  public function getURI($absolute = true)
  {
    $url = APP_MEDIA_DIR . '/' . $this->uri;
    if ($absolute) {
      $url = APP_URL . '/' . $url;
    }
    return $url;
  }

  /**
   * Performs a rotation of the photo and its thumbnail
   *
   * @param int $angle
   */
  public function rotate($angle)
  {
    $filenamePrefix = $this->getCleanTitle() . '_' . uniqid();

    $filename = $filenamePrefix . '.' . $this->mediaSubType;
    $photoFile = new File_Photo($this->getURI(false));
    $photoFile->rotate($angle, $this->getMediaSubType());
    $photoFile->rename($filename);
    $this->uri = $filename;

    $thumbnailFilename = $filenamePrefix . '.' . $this->thumbnailSubType;
    $thumbnail = new File_Photo($this->getThumbnailURI(false));
    $thumbnail->rotate($angle, $this->getThumbnailSubType());
    $thumbnail->rename($thumbnailFilename);
    $this->thumbnailUri = $thumbnailFilename;

    if ($angle != 180) {
      // Only update dimensions if we rotate 90 degrees
      $temp = $this->width;
      $this->width = $this->height;
      $this->height = $temp;

      $temp = $this->thumbnailWidth;
      $this->thumbnailWidth = $this->thumbnailHeight;
      $this->thumbnailHeight = $temp;
    }

    $this->save();
  }

  public function getLink()
  {
    return '/photo/' . $this->getCleanTitle() . '_' . $this->getId() . '/';
  }
}