<?php
class Lib_Form_Element_DateTime_Simple extends Lib_Form_Element_DateTime
{
    public function getValueFromDatabase($value)
    {
        return $value;
    }

    public function getFormattedValueForDatabase($value)
    {
        return $value;
    }
}