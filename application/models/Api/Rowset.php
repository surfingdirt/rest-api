<?php

abstract class Api_Rowset extends Zend_Db_Table_Rowset_Abstract
{
  public function getDataForClient()
  {
    $ret = array();

    foreach ($this as $row) {
      $ret[] = $row->getDataForClient();
    }

    return $ret;
  }
}