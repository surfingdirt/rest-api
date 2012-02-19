<?php
class AlbumsController extends Api_Controller_Action
{
	public $listCount = 20;

	protected function _preObjectCreation($object, $data)
	{
		$object->albumType = Media_Album::TYPE_SIMPLE;
		$object->albumAccess = Media_Album::ACCESS_PUBLIC;
		$object->albumCreation = Media_Album::CREATION_USER;
		$object->setNotification(true);
	}
	
    protected function _getWhereClause(User_Row $user)
    {
		if(in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))){
			$return = '1';
		} else {
			$return = $this->_table->getAdapter()->quoteInto('status = ?', Data::VALID);
		}
		
		$return .= $this->_table->getAdapter()->quoteInto(' AND albumType = ?', Media_Album::TYPE_SIMPLE);
		$return .= $this->_table->getAdapter()->quoteInto(' AND albumAccess = ?', Media_Album::ACCESS_PUBLIC);
		
		return $return;
    }
}