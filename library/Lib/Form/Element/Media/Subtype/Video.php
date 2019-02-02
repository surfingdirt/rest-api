<?php
class Lib_Form_Element_Media_Subtype_Video extends Lib_Form_Element_Media_Subtype {
  protected static function getValidSubtypes() {
    return Media_Item_Video::$allowedMediaSubTypes;
  }

}