<?php

class Api_Checkin extends Data
{
  const ITEM_TYPE = 'checkin';

  protected $_name = Constants_TableNames::CHECKIN;
  protected $_rowClass = 'Api_Checkin_Row';
  protected $_rowsetClass = 'Api_Checkin_Rowset';

  protected $_referenceMap = array(
    'Spot' => array(
      'columns' => 'spot',
      'refTableClass' => 'Spot',
      'refColumns' => 'id'
    ),
    'LastEditor' => array(
      'columns' => 'last_editor',
      'refTableClass' => 'User',
      'refColumns' => User::COLUMN_USERID
    ),
    'Submitter' => array(
      'columns' => 'submitter',
      'refTableClass' => 'User',
      'refColumns' => User::COLUMN_USERID
    ),
  );
}