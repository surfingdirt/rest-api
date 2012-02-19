<?php
class Api_User_Accessor extends Api_Data_Accessor
{
	public $publicReadAttributes = array(
		'userId',
		'username',
		'date',
		'lang',
		'country',
		'city',
		'zip',
		'gender',
		'level',
		'gear',
		'otherSports',
		'rideType',
		'avatar',
		'latitude',
		'longitude'
	);
	public $memberReadAttributes = array(
		'lastLogin',
		'firstName',
		'lastName',
		'site',
		'occupation',
	);
	public $ownReadAttributes = array(
		'email',
		'birthDate',
	);
	public $adminReadAttributes = array(
		'status'
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
		'lang' => 'lang',
		'country' => 'country',
		'city' => 'city',
		'zip' => 'zip',
		'gender' => 'gender',
		'level' => 'level',
		'gear' => 'gear',
		'otherSports' => 'otherSports',
		'rideType' => 'rideType',
		'avatar' => 'avatar',
		'firstName' => 'firstName',
		'lastName' => 'lastName',
		'site' => 'site',
		'occupation' => 'occupation',
		'email' => 'email',
		'birthDate' => 'birthDate',
		'userP' => 'password',
		'userPC' => 'password',
		'latitude' => 'latitude',
		'longitude' => 'longitude',

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
			//$data = $form->getFormattedValuesForDatabase();

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

			$object->status = User::STATUS_PENDING;
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
