<?php

class Api_User_Accessor extends Api_Data_Accessor
{
  public $publicReadAttributes = array(
    'avatar',
    'bio',
    'city',
    'cover',
    'date',
    'firstName',
    'locale',
    'timezone',
    'lastName',
    'site',
    'userId',
    'username',
    'album',
    'actions',
  );
  public $memberReadAttributes = array();
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
    'email' => 'email',
    'locale' => 'locale',
    'timezone' => 'timezone',
  );
  public $memberCreateAttributes = array();
  public $adminCreateAttributes = array();

  public $publicWriteAttributes = array();
  public $memberWriteAttributes = array();
  public $ownWriteAttributes = array(
    'avatar' => 'avatar',
    'bio' => 'bio',
    'cover' => 'cover',
    'email' => 'email',
    'firstName' => 'firstName',
    'locale' => 'locale',
    'timezone' => 'timezone',
    'lastName' => 'lastName',
    'site' => 'site',
    User::INPUT_PASSWORD => 'password',
    User::INPUT_PASSWORD_CONFIRM => 'password',
    User::INPUT_PASSWORD_OLD => 'password',
  );
  public $adminWriteAttributes = array(
    'lastLogin' => 'lastLogin',
    'status' => 'status'
  );

  public function createOAuthUser($user, $data)
  {
    $form = new User_Form_OAuth_Register();
    $errors = array();
    if(!$form->isValid($data)) {
      $rawErrors = $form->getErrors();
      foreach ($rawErrors as $name => $err) {
        if (!empty($err)) {
          $errors[$name] = $err;
        }
      }
    } else {
      $user->email = $data['email'];
      $user->locale = $data['locale'];
      $user->timezone = $data['timezone'];
      $user->username = $data['username'];
      $user->password = Utils::getRandomKey(16);
      $user->salt = Utils::uuidV4();
      $user->{User::COLUMN_USERID} = Utils::uuidV4();
      $user->status = User::STATUS_MEMBER;
      $user->date = Utils::date('Y-m-d H:i:s');
      $user->lastLogin = Utils::date('Y-m-d H:i:s');
      $user->activationKey = Utils::getRandomKey(32);

      try {
        $user->save();
      } catch (Exception $e) {
        $errors[] = $e->getMessage();
      }
    }

    return array($user->getId(), $user, $errors);
  }

  public function createObjectWithData($object, $data)
  {
    $userId = Utils::uuidV4();
    $salt = Utils::uuidV4();
    $authManager = new Lib_Auth_Manager(Globals::getMainDatabase());

    $attributes = $this->getCreateAttributes($object);

    $errors = array();
    $form = $object->getForm($this->_user, $this->_acl, $data);
    if (!$form->isValid($data)) {
      $rawErrors = $form->getErrors();
      foreach ($rawErrors as $name => $err) {
        if (!empty($err)) {
          $errors[$name] = $err;
        }
      }
    } else {
      foreach ($attributes as $attrFormName => $attrDBName) {
        if (!isset($data[$attrFormName])) {
          continue;
        }

        if ($attrFormName == User::INPUT_PASSWORD) {
          $target = $authManager->makeSaltedHash($data[$attrFormName], $salt);
        } else {
          $target = $data[$attrFormName];
        }

        $object->$attrDBName = $target;
      }

      $object->salt = $salt;
      $object->{User::COLUMN_USERID} = $userId;
      $object->status = User::STATUS_PENDING;
      $object->date = Utils::date('Y-m-d H:i:s');
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
    $where = $table->getAdapter()->quoteInto(User::COLUMN_USERID . ' = ?', $object->getId());
    $table->delete($where);

    return true;
  }

  protected function _updateKey($object, $attrFormName, $attrDBName, $rawData, $formattedData)
  {
    if (!isset($rawData[$attrFormName])) {
      return;
    }

    $target = isset($formattedData[$attrFormName]) ? $formattedData[$attrFormName] : $rawData[$attrFormName];
    if ($attrFormName == 'userPO') {
      return;
    }
    if ($attrFormName == 'userPC') {
      return;
    }
    if ($attrFormName == 'userP') {
      // TODO
      $authManager = new Lib_Auth_Manager(Globals::getMainDatabase());
      $target = $authManager->makeSaltedHash($target, $object->salt);
    }
    if (isset($rawData[$attrFormName]) && Lib_Translate::isTranslatedField($attrFormName)) {
      $field = $rawData[$attrFormName];
      if (!$object->$attrFormName) {
        Lib_Translate::setAsOriginal($field);
      }
      $object->$attrDBName = Lib_Translate::encodeField($field);
      return;
    }

    $object->$attrDBName = $target;
  }

  /**
   * Gets the list of attributes that can be read by
   * the given user.
   */
  public function getReadAttributes($object)
  {
    if (!$object->isReadableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised('Access unauthorised for user ' . $this->_user->getId());
    }

    $attr = $this->publicReadAttributes;
    if ($this->_user->isLoggedIn()) {
      $attr = array_merge($attr, $this->memberReadAttributes);
    }
    if ($this->_user->getId() == $object->getId()) {
      $attr = array_merge($attr, $this->ownReadAttributes);
    }
    if ($this->_user->isAdmin()) {
      $attr = array_merge($attr, $this->ownReadAttributes);
      $attr = array_merge($attr, $this->adminReadAttributes);
    }

    if (empty($attr)) {
      throw new Api_Exception_Unauthorised('No readable attributes for user ' . $this->_user->getId());
    }

    return $attr;
  }

  protected function _addEntriesForAttribute($attr, $object, $ret)
  {
    if ($attr === 'bio') {
      $ret[$attr] = Lib_Translate::decodeField($object->bio);
      return $ret;
    }

    return parent::_addEntriesForAttribute($attr, $object, $ret);
  }
}
