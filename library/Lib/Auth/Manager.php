<?php

class Lib_Auth_Manager
{
  public function __construct($db)
  {
    $this->_db = $db;
    $this->_authorizedLevels = implode(', ', array(
      "'" . User::STATUS_MEMBER . "'",
      "'" . User::STATUS_EDITOR . "'",
      "'" . User::STATUS_WRITER . "'",
      "'" . User::STATUS_ADMIN . "'",
    ));
  }

  public function getResultRowObject($username, $password)
  {

  }

  public function verifyLogin($username, $password)
  {
    $userIdColumn = User::COLUMN_USERID;
    $passwordColumn = User::COLUMN_PASSWORD;
    $usernameColumn = User::COLUMN_USERNAME;
    $statusColumn = User::COLUMN_STATUS;
    $table = Constants_TableNames::USER;

    $sql = <<<SQL
      SELECT {$passwordColumn} as hash, salt, {$userIdColumn} as userId
      FROM {$table}
      WHERE {$usernameColumn} = '{$username}'
      AND {$statusColumn} IN ({$this->_authorizedLevels});  
SQL;
    $stmt = $this->_db->query($sql);
    $data = $stmt->fetch();
    if (empty($data)) {
     return false;
    }

    $isValid = $data['hash'] === $this->makeSaltedHash($password, $data['salt']);

    return $isValid ? $data['userId'] : null;
  }

  public function makeSaltedHash($password, $salt)
  {
    return md5("$password.$salt");
  }

}