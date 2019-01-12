<?php
class Api_User_Accessor extends Api_Data_Accessor
{
	public $publicReadAttributes = array(
    'avatar',
    'city',
    'country',
    'date',
    'firstName',
    'lang',
    'lastName',
    'latitude',
    'longitude',
    'site',
		'userId',
		'username',
    'zip',
	);
	public $memberReadAttributes = array(
	);
	public $ownReadAttributes = array(
		'email',
	);
	public $adminReadAttributes = array(
    'lastLogin',
		'status',
	);

	public $publicCreateAttributes = array(
		'username' => 'username',
		'userP' => 'password',
		'email' => 'email'
	);
	public $memberCreateAttributes = array();
	public $adminCreateAttributes = array();

	public $publicWriteAttributes = array();
	public $memberWriteAttributes = array();
	public $ownWriteAttributes = array(
    'avatar' => 'avatar',
    'city' => 'city',
		'country' => 'country',
    'email' => 'email',
    'firstName' => 'firstName',
    'lang' => 'lang',
		'lastName' => 'lastName',
    'latitude' => 'latitude',
    'longitude' => 'longitude',
		'site' => 'site',
		'userP' => 'password',
		'userPC' => 'password',
    'zip' => 'zip',
	);
	public $adminWriteAttributes = array(
		'lastLogin' => 'lastLogin',
		'status' => 'status'
	);

	public function createObjectWithData($object, $data)
	{
		$attributes = $this->getCreateAttributes($object);
		$log = '';

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
					$log .= 'skipping '.$attrFormName.' -- ';
					continue;
				}

				if($attrFormName == 'userP'){
					$target = md5($data[$attrFormName]);
				} else {
					$target = $data[$attrFormName];
				}

				$log .= 'setting '.$attrDBName.' = ' . $target . ' -- ';
				$object->$attrDBName = $target;
			}
			//error_log($log);

			$object->status = User::STATUS_MEMBER;
			$object->date = Utils::date('Y-m-d H:i:s');
        	$object->lang = Globals::getTranslate()->getLocale();
        	$object->activationKey = Utils::getRandomKey(32);

			$object->save();
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
