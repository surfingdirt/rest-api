<?php

class Api_Image_Row extends Api_Data_Row
{
  /**
   * Do not notify users when an image is created
   *
   * @var boolean
   */
  protected $_defaultNotification = false;

  public function isDeletableBy(User_Row $user, Lib_Acl $acl)
  {
    if ($this->submitter === $user->getId()) {
      return true;
    }

    return $user->isAdmin() || $user->isEditor();
  }

  // No need for that
  public function _saveTranslatedTexts()
  {
  }
}