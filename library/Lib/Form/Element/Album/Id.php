<?php
class Lib_Form_Element_Album_Id extends Lib_Form_Element_Album
{
    /**
     * Default element name
     *
     * @var string
     */
    protected $_defaultName = 'albumId';
    /**
     * Validator
     *
     * @var string
     */
    protected $_validator = 'Lib_Validate_Album_Id';
}