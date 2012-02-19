<?php
class Api_Media extends Media_Item
{
	protected $_rowClass = 'Api_Media_Row';
	/* No $_rowSet overloading as we don't want to allow listing media */	
}