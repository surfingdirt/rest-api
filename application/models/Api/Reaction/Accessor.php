<?php
class Api_Reaction_Accessor extends Api_Data_Accessor
{
  protected $_disregardUpdates = array(
    'date'
  );

  public $publicReadAttributes = array(
    'itemId',
    'itemType',
    'type',
    'userId',
  );

  public $memberCreateAttributes = array(
    'itemId' => 'itemId',
    'itemType' => 'itemType',
    'type' => 'type',
    'userId' => 'userId',
  );

  public $ownWriteAttributes = array(
    'itemId' => 'itemId',
    'itemType' => 'itemType',
    'type' => 'type',
    'userId' => 'userId',
  );

  public function createObjectWithData($object, $data)
  {
    $object->id = Utils::uuidV4();
    $object->submitter = $this->_user->getId();

    return parent::createObjectWithData($object, $data);
  }
}
// TODO: save on POST or PUT