<?php

class Comment_Row extends Data_Row implements Data_Row_MetaDataInterface
{
  /**
   * Object being commented
   *
   * @var Data_Row
   */
  public $parentItem;

  /**
   * Name of the route used to construct creation urls
   *
   * @var string
   */
  protected $_createRoute = 'postcomment';

  /**
   * Name of the route used to construct edition urls
   *
   * @var string
   */
  protected $_editRoute = 'editcomment';

  /**
   * Name of the route used to construct delete urls
   *
   * @var string
   */
  protected $_deleteRoute = 'deletecomment';

  /**
   * Name of the class of form used to edit this object
   *
   * @var string
   */
  protected $_formClass = 'Comment_Form';

  protected $_hasReactions = true;

  /**
   * Returns the parent item
   * @return Data_Row
   * @throws Lib_Exception
   */
  public function getParentItemfromDatabase()
  {
    $dataTableName = Data::mapDataType($this->parentType);
    if ($dataTableName === 'Media_Album') {
      return Media_Album_Factory::buildAlbumById($this->parentId);
    }

    $dataTable = new $dataTableName();

    $parentItem = $this->parentItem = $dataTable->find($this->parentId)->current();
    return $parentItem;
  }

  public function getParentItem()
  {
    $parentRow = $this->getParentItemfromDatabase();
    return $parentRow;
  }

  /**
   * Comment content is translated
   *
   * @param string $columnName
   * @return boolean
   */
  protected function _isTranslated($columnName)
  {
    return false;
  }

  public function getTone()
  {
    return $this->tone;
  }

  /**
   * Returns the title, from this table or another one
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->id;
  }

  /**
   * Returns the description, from this table or another one
   *
   * @return string
   */
  public function getDescription()
  {
    throw new Lib_Exception("Comments cannot be asked for their description");
  }

  /**
   * Returns the content of this object
   *
   * @return string
   */
  public function getContent()
  {
    return Lib_Translate::decodeField($this->content);
  }

  /**
   * No folders for comments
   */
  public function getFolderPath()
  {
  }

  /**
   * Returns the cache id for the comments attached to the current user
   *
   * @return unknown
   */
  protected function _getCommentsCacheId()
  {
    // Here we're perverting this function's return value by returning the cache id for the parent's comment list
    $cacheId = 'commentsFor_' . $this->parentType . $this->_getIdForCache($this->parentId);
    return $cacheId;
  }
}