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
    $saltedPassword = $this->_getSaltedPassword($password, $data['salt']);
    $isValid = password_verify($saltedPassword, $data['hash']);

    return $isValid ? $data['userId'] : null;
  }

  public function makeSaltedHash($password, $salt)
  {
    $options = [
      'cost' => 12,
    ];
    $saltedPassword = $this->_getSaltedPassword($password, $salt);
    return password_hash($saltedPassword, PASSWORD_BCRYPT, $options);
  }

  protected function _getSaltedPassword($password, $salt)
  {
    return "$password.$salt";
  }
}