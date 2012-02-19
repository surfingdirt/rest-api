<?php
interface Api_Data_Row_Interface
{
	/**
	 * Returns the data that is 'public',
	 * that is to say, visible to the client.
	 * @param User_Row $user
	 * @param Lib_Acl $acl
	 * @throws Api_Forbidden_Exception if user has no access
	 */
	public function getDataForClient(User_Row $user, Lib_Acl $acl);

	/**
	 * Gets the list of attributes that can be read by
	 * the given user.
	 * @param User_Row $user
	 */
	public function getReadAttributes(User_Row $user);

	/**
	 * Gets the list of attributes that can be written by
	 * the given user.
	 * @param User_Row $user
	 */
	public function getWriteAttributes(User_Row $user);
}