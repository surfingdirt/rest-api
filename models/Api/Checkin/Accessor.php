<?php

class Api_Checkin_Accessor extends Api_Data_Accessor
{
  protected $_disregardUpdates = array(
    'tags',
  );

  public $publicReadAttributes = array(
    'id',
    'submitter',
    'spot',
    'checkinDate',
    'checkinDuration',
    'date',
    'lastEditor',
    'lastEditionDate',
    'status',
    'country',
    'region',
    'latitude',
    'longitude'
  );

  public $memberCreateAttributes = array(
    'spot' => 'spot',
    'checkinDate' => 'checkinDate',
    'checkinDuration' => 'checkinDuration',
    'status' => 'status',
  );

  public $ownWriteAttributes = array(
    'spot' => 'spot',
    'checkinDate' => 'checkinDate',
    'checkinDuration' => 'checkinDuration',
  );

  public $adminWriteAttributes = array();

  public function createObjectWithData($object, $data)
  {
    if (!isset($data['checkinDate']) || empty($data['checkinDate'])) {
      $data['checkinDate'] = Utils::date('Y-m-d H:i:s');
    }

    if (!isset($data['checkinDuration']) || empty($data['checkinDuration'])) {
      $data['checkinDuration'] = DEFAULT_CHECKIN_DURATION;
    }

    return parent::createObjectWithData($object, $data);
  }
}