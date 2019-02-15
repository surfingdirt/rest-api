<?php

class Api_Image extends Api_Data
{
  const IMAGE_TYPE_PLAIN = 0;
  const IMAGE_TYPE_THUMB = 1;

  protected $_name = Constants_TableNames::IMAGE;

  protected $_rowClass = 'Api_Image_Row';
  protected $_rowsetClass = 'Api_Image_Rowset';

  public function getItemType()
  {
    return 'image';
  }

  public static function cleanupById($storageType, $id)
  {
    // Find the folder, and delete it
    Lib_Storage::cleanUpFiles($storageType, $id);

    $table = new self();
    $result = $table->find($id);
    if (empty($result) || !$imageRow = $result->current()) {
      // All done
      return;
    }

    $imageRow->delete();
  }
}