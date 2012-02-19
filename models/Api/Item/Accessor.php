<?php
class Api_Item_Accessor extends Api_Data_Accessor
{
	public $publicReadAttributes = array(
		'id',
		'date',
		'submitter',
		'status',
		'itemId',
		'itemType'
	); 
}