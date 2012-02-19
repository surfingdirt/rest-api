<?php
class Api_Media_Form_Photo extends Media_Item_Photo_Form
{
	protected function _buildElements()
	{
		$elements = parent::_buildElements();
		$elements['albumId'] = $this->_buildAlbumId();
		return $elements;
	}

    public function getTitle()
    {
        $element = new Data_Form_Element_Title($this);
        $element->setRequired(!($this->_object->getId()));
        return $element;
    }
    
    public function getDescription()
    {
        $element = new Data_Form_Element_Description($this);
        $element->setRequired(!($this->_object->getId()));
        return $element;
    }
    
	protected function _buildAlbumId()
	{
		return new Lib_Form_Element_Album_Id('albumId', false, true, true);
	}	
}