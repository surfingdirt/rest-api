<?php

class Api_Spot_Accessor extends Api_Data_Accessor
{
  protected $_disregardUpdates = array(
    'tags',
    'submit',
    'skipAutoFields',
    'longitude',
    'latitude',
    'zoom',
    'yaw',
    'pitch',
    'mapType',
    'locationFlag'
  );

  public $publicReadAttributes = array(
    'id',
    'title',
    'description',
    'date',
    'submitter',
    'lastEditor',
    'lastEditionDate',
    'dpt',
    'longitude',
    'latitude',
    'status',
    'difficulty',
    'spotType',
    'groundType'
  );

  public $memberCreateAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'status' => 'status',
    'dpt' => 'dpt',
    'longitude' => 'longitude',
    'latitude' => 'latitude',
    'difficulty' => 'difficulty',
    'spotType' => 'spotType',
    'groundType' => 'groundType',
  );

  public $ownWriteAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'dpt' => 'dpt',
    'longitude' => 'longitude',
    'latitude' => 'latitude',
    'difficulty' => 'difficulty',
    'spotType' => 'spotType',
    'groundType' => 'groundType',
  );

  public $adminWriteAttributes = array(
    'status' => 'status',
  );

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
      //$data = $form->getFormattedValuesForDatabase();

      foreach ($attributes as $attrFormName => $attrDBName) {
        if (!isset($data[$attrFormName])) {
          continue;
        }

        if (in_array($attrFormName, array('longitude', 'latitude'))) {
          continue;
        }
        $object->$attrDBName = $data[$attrFormName];
      }

      $object->save();
      $this->_createLocation($object, $data);
    }
    return array($object->getId(), $object, $errors);
  }

  protected function _manageLocation($object, array $data)
  {
    $location = $object->getLocation();
    if ($location && (isset($data['longitude']) && is_null($data['longitude']) ||
        isset($data['latitude']) && is_null($data['latitude']))) {
      // Deleting an existing location
      $location->delete();
      return;
    }

    if (empty($data['longitude']) || empty($data['latitude'])) {
      return;
    }

    if (!$location) {
      $table = new Location();
      $location = $table->fetchNew();
    }

    // Creating/updating
    $location->longitude = $data['longitude'];
    $location->latitude = $data['latitude'];
    /*
    $location->zoom = $data['zoom'];
    $location->yaw = $data['yaw'];
    $location->pitch = $data['pitch'];
    $location->mapType = $data['mapType'];
    */
    $location->status = Data::VALID;
    $location->itemId = $object->getId();
    $location->itemType = $object->getItemType();
    $location->save();
  }

  protected function _createLocation($object, array $data)
  {
    if (empty($data['longitude']) || empty($data['latitude'])) {
      return;
    }

    $table = new Location();
    $location = $table->fetchNew();

    // Creating/updating
    $location->longitude = $data['longitude'];
    $location->latitude = $data['latitude'];
    $location->status = Data::VALID;
    $location->itemId = $object->getId();
    $location->itemType = $object->getItemType();
    $location->save();
  }
}