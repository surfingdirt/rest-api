<?php
class Api_Country_Accessor extends Api_Data_Accessor
{
	public $publicReadAttributes = array(
		'id',
		'title',
		'simpleTitle',
		'lang',
		'status',
		'bounds'
	);    
}