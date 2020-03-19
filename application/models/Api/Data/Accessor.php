<?php

abstract class Api_Data_Accessor
{
  protected $_user;
  protected $_acl;

  public $publicReadAttributes = array();
  public $memberReadAttributes = array();
  public $ownReadAttributes = array();
  public $adminReadAttributes = array();

  public $publicCreateAttributes = array();
  public $memberCreateAttributes = array();
  public $adminCreateAttributes = array();

  public $publicWriteAttributes = array();
  public $memberWriteAttributes = array();
  public $ownWriteAttributes = array();
  public $adminWriteAttributes = array();
  public $forbiddenWriteAttributes = array();


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
    if (!$object->isReadableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised('Access unauthorised for user ' . $this->_user->getId());
    }

    $attr = $this->publicReadAttributes;
    if ($this->_user->isLoggedIn()) {
      $attr = array_merge($attr, $this->memberReadAttributes);
    }
    if ($this->_user->getId() == $object->getSubmitter()->getId()) {
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

  /**
   * Performs a read operation
   */
  public function getObjectData($object, $action = 'list')
  {
    if (!$object) {
      throw new Api_Exception_NotFound();
    }
    $attributes = $this->getReadAttributes($object);
    $ret = array();
    foreach ($attributes as $attr) {
      $ret = $this->_addEntriesForAttribute($attr, $object, $ret);
    }

    return $ret;
  }

  /**
   * Adds a representation of the object attribute
   * @param string $attr
   * @param Data_Row|User_Row $object
   * @param array $ret
   */
  protected function _addEntriesForAttribute($attr, $object, $ret)
  {
    $nameOrIdAttrs = array(
      'submitter',
      'lastEditor',
      'dpt',
      'spot',
      'trick',
      'album',
      'author',
      'toUser',
      'country',
      'region',
    );

    if (in_array($attr, array('longitude', 'latitude'))) {
      if ($location = $object->getLocation()) {
        $ret[$attr] = $location->$attr;
      } else {
        $ret[$attr] = null;
      }
    } elseif (in_array($attr, $nameOrIdAttrs)) {
      switch ($attr) {
        case 'submitter':
          $otherObject = $object->getSubmitter();
          break;
        case 'lastEditor':
          $otherObject = $object->getLastEditor();
          break;
        case 'spot':
          $otherObject = $object->getSpot();
          break;
        case 'dpt':
          $otherObject = $object->getDpt();
          break;
        case 'trick':
          $otherObject = $object->getTrick();
          break;
        case 'album':
          $otherObject = $object->getAlbum();
          break;
        case 'author':
          $otherObject = $object->getAuthor();
          break;
        case 'toUser':
          $table = new Api_User();
          $results = $table->find($object->$attr);
          if ($results) {
            $otherObject = $results->current();
          } else {
            $otherObject = null;
          }

          break;
        case 'country':
          $otherObject = $object->getCountry();
          break;
        case 'region':
          $otherObject = $object->getRegion();
          break;
        default:
          // Should not happen
          throw new Api_Exception('wtf?');
          break;
      }

      $id = $name = null;
      if (is_object($otherObject)) {
        $id = $otherObject->getId();
        $name = $otherObject->getTitle();
      } elseif (!empty($otherObject)) {
        $name = $otherObject;
      }

      $ret[$attr] = array(
        'id' => $id,
        'title' => $name
      );

    } elseif ($attr == 'actions') {
      $ret[$attr] = $this->getActions($object);
    } elseif ($attr == 'users') {
      $ret[$attr] = $object->getUserIdsInMedia();
    } elseif ($attr == 'bounds') {
      $ret[$attr] = $object->getBounds();
    } elseif ($attr == 'title') {
      $ret[$attr] = $object->getTitle();
    } elseif ($attr == 'description') {
      $ret[$attr] = $object->getDescription();
    } elseif ($attr == 'content') {
      $ret[$attr] = $object->getContent();
    } elseif (isset($object->$attr)) {
      $ret[$attr] = $object->$attr;
    }
    return $ret;
  }

  public function getActions($object)
  {
    $actions = array();
    $actions['edit'] = $object->isEditableBy($this->_user, $this->_acl);
    $actions['delete'] = $object->isDeletableBy($this->_user, $this->_acl);
    return $actions;
  }

  /**
   * Gets the list of attributes available for POST operations
   */
  public function getCreateAttributes($object)
  {
    $attr = array();
    if (!$object->isCreatableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised('Access unauthorised for user ' . $this->_user->getId());
    }

    if ($this->_user->isLoggedIn()) {
      $attr = array_merge($attr, $this->memberCreateAttributes);
    } else {
      $attr = array_merge($attr, $this->publicCreateAttributes);
    }

    if ($this->_user->isAdmin()) {
      $attr = array_merge($attr, $this->adminCreateAttributes);
    }

    if (empty($attr)) {
      throw new Api_Exception_Unauthorised('No creatable attributes for user ' . $this->_user->getId());
    }

    return $attr;
  }

  /**
   * Gets the list of attributes available for PUT operations
   */
  public function getUpdateAttributes($object)
  {
    $attr = array();
    if (!$object->isEditableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised('Access unauthorised for user ' . $this->_user->getId());
    }

    if ($this->_user->isLoggedIn()) {
      $attr = array_merge($attr, $this->memberWriteAttributes);
    } else {
      $attr = array_merge($attr, $this->publicWriteAttributes);
    }
    if ($this->_user->getId() == $object->getSubmitter()->getId()) {
      $attr = array_merge($attr, $this->ownWriteAttributes);
    }
    if ($this->_user->isAdmin() || $this->_user->isEditor()) {
      $attr = array_merge($attr, $this->ownWriteAttributes);
      $attr = array_merge($attr, $this->adminWriteAttributes);
    }

    if (empty($attr)) {
      throw new Api_Exception_Unauthorised('No writable attributes for user ' . $this->_user->getId());
    }

    return $attr;
  }

  /**
   * Creates an object for a POST operations
   */
  public function createObjectWithData($object, $data)
  {
    $attributes = $this->getCreateAttributes($object);


    $errors = array();
    $form = $object->getForm($this->_user, $this->_acl);
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
        if (Lib_Translate::isTranslatedField($attrFormName)) {
          $fieldData = $data[$attrFormName];
          Lib_Translate::setAsOriginal($fieldData);
          $object->$attrDBName = Lib_Translate::encodeField($fieldData);
        } else {
          $object->$attrDBName = $data[$attrFormName];
        }
      }
      $object->save();
    }
    return array($object->getId(), $object, $errors);
  }

  /**
   * Updates an object for a PUT operations
   */
  public function updateObjectWithData($object, $data)
  {
    $attributes = $this->getUpdateAttributes($object);
    // Validation
    $form = $object->getForm($this->_user, $this->_acl, $data);
    if (!$form->isValid($data)) {
      $errors = $form->getNonEmptyErrors();
      return $errors;
    }

    // Updates
    $data = $this->_mergeDataForUpdate($form->populateFromDatabaseData($object->toArray()), $data, $attributes);
    $form = $object->getForm($this->_user, $this->_acl, $data);
    $formattedData = $form->getFormattedValuesForDatabase();
    foreach ($attributes as $attrFormName => $attrDBName) {
      $this->_updateKey($object, $attrFormName, $attrDBName, $data, $formattedData);
    }
    $object->save();
    return array();
  }

  protected function _mergeDataForUpdate($existingData, $newData, $attributes) {
    $ret = [];
    foreach($existingData as $key => $existing) {
      // Keep existing data
      $ret[$key] = $existing;
      if (isset($newData[$key])) {
        if (Lib_Translate::isTranslatedField($key)) {
          // We're changing the meaning of the content: wipe out everything but the new content
          // No need for this branch in the end, but the comment is useful
          $ret[$key] = $newData[$key];
          if (!Lib_Translate::hasOriginal($ret[$key])) {
            Lib_Translate::setAsOriginal($ret[$key]);
          }
        } else {
          // Overwrite with new one
          $ret[$key] = $newData[$key];
        }
      }
    }

    foreach ($newData as $key => $new) {
      if (!array_key_exists($key, $existingData) && isset($attributes[$key])) {
        $ret[$key] = $new;
      }
    }
    return $ret;
  }

  public function deleteObject($object)
  {
    $object->delete();
    return true;
  }

  /**
   * Updates the data in $object
   * @param $object
   * @param $key
   * @param $rawData
   * @param $formattedData
   */
  protected function _updateKey($object, $attrFormName, $attrDBName, $rawData, $formattedData)
  {
    if (!isset($rawData[$attrFormName])) {
      return;
    }

    $target = isset($formattedData[$attrFormName]) ? $formattedData[$attrFormName] : $rawData[$attrFormName];
    if (Lib_Translate::isTranslatedField($attrDBName)) {
      $target = Lib_Translate::encodeField($target);
    }
    $object->$attrDBName = $target;
  }
}