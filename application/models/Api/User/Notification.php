<?php

class Api_User_Notification extends User_Notification
{
  protected $_rowClass = 'Api_User_Notification_Row';

  protected $_referenceMap = array(
    'Api_User' => array(
      'columns' => 'userId',
      'refTableClass' => 'Api_User',
      'refColumns' => User::COLUMN_USERID
    ),
  );
}