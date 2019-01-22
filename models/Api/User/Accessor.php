<?php
class Api_User_Accessor extends Api_Data_Accessor
{
	public $publicReadAttributes = array(
    'avatar',
    'city',
    'date',
    'firstName',
    'lang',
    'lastName',
    'site',
		'userId',
		'username',
	);
	public $memberReadAttributes = array(
	);
	public $ownReadAttributes = array(
		'email',
    'status',
	);
	public $adminReadAttributes = array(
    'lastLogin',
		'status',
	);

	public $publicCreateAttributes = array(
		'username' => 'username',
    User::INPUT_PASSWORD => 'password',
		'email' => 'email'
	);
	public $memberCreateAttributes = array();
	public $adminCreateAttributes = array();

	public $publicWriteAttributes = array();
	public $memberWriteAttributes = array();
	public $ownWriteAttributes = array(
    'avatar' => 'avatar',
    'email' => 'email',
    'firstName' => 'firstName',
    'lang' => 'lang',
		'lastName' => 'lastName',
		'site' => 'site',
    User::INPUT_PASSWORD => 'password',
    User::INPUT_PASSWORD_CONFIRM => 'password',
	);
	public $adminWriteAttributes = array(
		'lastLogin' => 'lastLogin',
		'status' => 'status'
	);

	public function createObjectWithData($object, $data)
	{
		$attributes = $this->getCreateAttributes($object);

		$errors = array();
		$form = $object->getForm($this->_user, $this->_acl);
		if(!$form->isValid($data)){
			$rawErrors = $form->getErrors();
			foreach($rawErrors as $name => $err){
				if(!empty($err)){
					$errors[$name] = $err;
				}
			}
		} else {
			foreach($attributes as $attrFormName => $attrDBName){
				if(!isset($data[$attrFormName])){
					continue;
				}

				if($attrFormName == User::INPUT_PASSWORD){
					$target = md5($data[$attrFormName]);
				} else {
					$target = $data[$attrFormName];
				}

				$object->$attrDBName = $target;
			}

      $object->{User::COLUMN_USERID} = Utils::uuidV4();
			$object->status = User::STATUS_PENDING;
			$object->date = Utils::date('Y-m-d H:i:s');
			$object->lang = Globals::getTranslate()->getLocale();
			$object->activationKey = Utils::getRandomKey(32);
			$object->save();

			// Update the user object so that the response can contain all user-visible properties.
			$this->_user = $object;
			Globals::setUser($object);
		}
		return array($object->getId(), $object, $errors);
	}

	public function deleteObject($object)
	{
        $table = $object->getTable();
        $where = $table->getAdapter()->quoteInto(User::COLUMN_USERID .' = ?', $object->getId());
        $table->delete($where);

        return true;
	}

	protected function _updateKey($object, $attrFormName, $attrDBName, $rawData, $formattedData)
	{
		if(!isset($rawData[$attrFormName])){
			return;
		}

		$target = isset($formattedData[$attrFormName]) ? $formattedData[$attrFormName] : $rawData[$attrFormName];
		if($attrFormName == 'userPC'){
			return;
		}
		if($attrFormName == 'userP'){
			$target = md5($target);
		}
		$object->$attrDBName = $target;
	}
}
