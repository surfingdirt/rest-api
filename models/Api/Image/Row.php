<?php

class Api_Image_Row extends Api_Data_Row
{
  public function isDeletableBy(User_Row $user, Lib_Acl $acl)
  {
    if ($this->submitter === $user->getId()) {
      return true;
    }

    return $acl->isAllowed($user, Lib_Acl::PUBLIC_EDIT_RESOURCE);
  }

  // No need for that
  public function _saveTranslatedTexts()
  {
  }
}