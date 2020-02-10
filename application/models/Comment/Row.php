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
    if (!$parentRow) {
      return null;
    }

    $itemTable = new Item();

    $adapter = $itemTable->getAdapter();
    $where = $adapter->quoteInto('itemType = ?', $parentRow->getItemType());
    $where .= $adapter->quoteInto(' AND itemId = ?', $parentRow->id);

    $parentItem = $itemTable->fetchRow($where);
    return $parentItem;
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
    $content = json_decode($this->content, true);
    return $content;
  }

  /**
   * No folders for comments
   */
  public function getFolderPath()
  {
  }
}