<?php

class Api_Trick_Accessor extends Api_Data_Accessor
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
    'status',
    'difficulty',
    'trickTip'
  );

  public $memberCreateAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'status' => 'status',
    'difficulty' => 'difficulty',
    'trickTip' => 'trickTip',
  );

  public $ownWriteAttributes = array(
    'title' => 'title',
    'description' => 'description',
    'difficulty' => 'difficulty',
    'trickTip' => 'trickTip',
  );

  public $adminWriteAttributes = array(
    'status' => 'status',
  );
}