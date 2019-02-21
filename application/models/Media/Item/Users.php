<?php

class Media_Item_Users extends Zend_Db_Table_Abstract
{
  protected $_name = Constants_TableNames::MEDIAITEMUSERS;

  protected $_rowClass = 'Media_Item_Users_Row';

  public function insertUsers($mediaId, array $users)
  {
    foreach ($users as $userId) {
      $this->insert(array(
        'mediaId' => $mediaId,
        'userId' => $userId,
      ));
    }
  }

  public function updateUsers($mediaId, $users)
  {
    if (!$users || sizeof($users) == 0) {
      return;
    }

    $this->delete($this->getAdapter()->quoteInto('mediaId = ?', $mediaId));
    $this->insertUsers($mediaId, $users);
  }
}