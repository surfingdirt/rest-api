<?php
class Media_Item_Users extends Zend_Db_Table_Abstract
{
    protected $_name = Constants_TableNames::MEDIAITEMUSERS;

    protected $_rowClass = 'Media_Item_Users_Row';
    
    public function insertUsers($mediaId, array $users)
    {
    	foreach($users as $username => $userId){
    		if($userId){
    			$this->insert(array(
	    			'mediaId' => $mediaId,
    				'userId' => $userId,
    			));
    		} else {
    			$this->insert(array(
	    			'mediaId' => $mediaId,
    				'userId' => 0,
    				'userName' => $username
    			));
    		}
    	}
    }
    
    public function updateUsers($mediaId, array $users)
    {
		$this->delete("mediaId = $mediaId");
    	$this->insertUsers($mediaId, $users);
    }
}