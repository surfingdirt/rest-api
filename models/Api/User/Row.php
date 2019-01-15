<?php
class Api_User_Row extends User_Row
{
	/**
	 * Implements the missing isReadableBy method (defined in Data_Row)
	 * @param User_Row $user
	 * @param Lib_Acl $acl
	 */
	public function isReadableBy(User_Row $user, Lib_Acl $acl)
	{
		if ($user->getId() === $this->getId()) {
		  return true;
    }

	  switch($this->status){
			case User::STATUS_GUEST:
				return false;
				break;
			case User::STATUS_BANNED:
			case User::STATUS_PENDING:
				if(in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR))){
					return true;
				}
				break;
			case User::STATUS_MEMBER:
			case User::STATUS_WRITER:
			case User::STATUS_EDITOR:
			case User::STATUS_ADMIN:
				return true;
				break;
			default:
				throw new Api_Exception("Unexpected status for user '".$this->getId()."': '".$this->status."'");
				break;
		}
	}

	public function isCreatableBy(User_Row $user, Lib_Acl $acl)
	{
		if($user->status == User::STATUS_GUEST){
			return true;
		}

		return false;
	}

	public function isEditableBy(User_Row $user, Lib_Acl $acl)
	{
		if($user->getId() == $this->getId()){
			return true;
		}

		switch($user->status){
			case User::STATUS_GUEST:
			case User::STATUS_BANNED:
			case User::STATUS_PENDING:
			case User::STATUS_MEMBER:
			case User::STATUS_WRITER:
			case User::STATUS_EDITOR:
				return false;
				break;
			case User::STATUS_ADMIN:
				return true;
				break;
			default:
				throw new Api_Exception("Unexpected status for user '".$this->getId()."': '".$this->status."'");
				break;
		}
	}

	public function isDeletableBy(User_Row $user, Lib_Acl $acl)
	{
			if($user->getId() == $this->getId()){
			return true;
		}

		switch($user->status){
			case User::STATUS_GUEST:
			case User::STATUS_BANNED:
			case User::STATUS_PENDING:
			case User::STATUS_MEMBER:
			case User::STATUS_WRITER:
			case User::STATUS_EDITOR:
				return false;
				break;
			case User::STATUS_ADMIN:
				return true;
				break;
			default:
				throw new Api_Exception("Unexpected status for user '".$this->getId()."': '".$this->status."'");
				break;
		}
	}

	public function getForm()
	{
		if($this->getId()){
			$form = new User_Form_Update($this);
		} else {
			$form = new User_Form_Register($this);
		}

		return $form;
	}

	/**
	 * Implements the missing getSubmitter method (defined in Data_Row)
	 */
	public function getSubmitter()
	{
		return $this;
	}
	
	public function getCountry()
	{
    	$table = new Country();
		$result = $table->find($this->country);
		if(empty($result)){
			return null;
		}
		$country = $result->current();
		return $country;
	}	
}