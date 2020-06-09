<?php
class Api_Reaction_Accessor extends Api_Data_Accessor
{
  protected $_disregardUpdates = array(
    'date'
  );

  public $publicReadAttributes = array(
    'id',
    'itemId',
    'itemType',
    'type',
    'submitter',
    'date',
  );

  public $memberCreateAttributes = array(
    'itemId' => 'itemId',
    'itemType' => 'itemType',
    'type' => 'type',
    'submitter' => 'submitter',
  );

  public $ownWriteAttributes = array(
    'itemId' => 'itemId',
    'itemType' => 'itemType',
    'type' => 'type',
    'submitter' => 'submitter',
  );

  public function createObjectWithData($object, $data)
  {
    $object->id = Utils::uuidV4();
    $object->submitter = $this->_user->getId();

    try {
      list($id, $object, $errors) = parent::createObjectWithData($object, $data);
      Api_Reaction::clearReactionCacheFor($object);

      return [$id, $object, $errors];
    } catch (Zend_Db_Statement_Exception $e) {
      // This is pretty hacky, it would be better served with a validator and a kind of DOES_NOT_EXIST constraint
      return [null, $object, array( 'itemId' => ['duplicatedEntry'] )];
    }
  }

  public function deleteObject($object)
  {
    Api_Reaction::clearReactionCacheFor($object);
    return parent::deleteObject($object);
  }

}