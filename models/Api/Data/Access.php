<?php
class Api_Data_Access
{
	protected $_user;
	protected $_acl;

	public function __construct(User_Row $user, Lib_Acl $acl)
	{
		$this->_user = $user;
		$this->_acl = $acl;
	}

	/**
	 * Gets the list of attributes that can be read by
	 * the given user.
	 */
	public function getReadAttributes($object)
	{
		$attr = array();
		if(!$object->isReadableBy($this->_user, $this->_acl)){
			throw new Api_Exception_Unauthorised('Access unauthorised for user '.$this->_user->getId());
		}

		$attr = $object->publicReadAttributes;
		if($this->_user->isLoggedIn()){
			$attr = array_merge($attr, $object->memberReadAttributes);
		}
		if($this->_user->getId() == $object->getSubmitter()->getId()){
			$attr = array_merge($attr, $object->ownReadAttributes);
		}
		if($this->_user->isAdmin() || $this->_user->isEditor()){
			$attr = array_merge($attr, $object->ownReadAttributes);
			$attr = array_merge($attr, $object->adminReadAttributes);
		}

		if(empty($attr)){
			throw new Api_Exception_Unauthorised('No readable attributes for user '.$this->_user->getId());
		}

		return $attr;
	}

	public function getObjectData($object)
	{
		$attributes = $this->getReadAttributes($object);
		$ret = array();
		foreach($attributes as $attr){
			$ret[$attr] = $object->$attr;
		}

		return $ret;
	}

	public function getCreateAttributes($object)
	{
		$attr = array();
		if(!$object->isCreatableBy($this->_user, $this->_acl)){
			throw new Api_Exception_Unauthorised('Access unauthorised for user '.$this->_user->getId());
		}

		if($this->_user->isLoggedIn()){
			$attr = array_merge($attr, $object->memberCreateAttributes);
		} else {
			$attr = array_merge($attr, $object->publicCreateAttributes);
		}

		if($this->_user->isAdmin() || $this->_user->isEditor()){
			$attr = array_merge($attr, $object->adminCreateAttributes);
		}

		if(empty($attr)){
			throw new Api_Exception_Unauthorised('No creatable attributes for user '.$this->_user->getId());
		}

		return $attr;
	}

	public function createObjectWithData($object, $data)
	{
		$attributes = $this->getCreateAttributes($object);
$log = '';
		$errors = array();
		$form = $object->getForm($this->_user, $this->_acl);
		if(!$form->isValid($data)){
			$errors = $form->getErrors();
		} else {
			foreach($attributes as $attrFormName => $attrDBName){
$log .= 'skipping '.$attrFormName.PHP_EOL;
				if(!isset($data[$attrFormName])){
					continue;
				}
$log .= 'setting '.$attrDBName.' = ' . $data[$attrFormName] . PHP_EOL;
				$object->$attrDBName = $data[$attrFormName];
			}
error_log($log);
			$object->save();
		}
		return array($object->getId(), $errors);
	}

	public function updateObjectWithData($object, $data)
	{
	}
}