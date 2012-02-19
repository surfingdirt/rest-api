<?php
class Api_PrivateMessage_Accessor extends Api_Data_Accessor
{
	public $publicReadAttributes = array(
		'id',
		'content',
		'date',
		'submitter',
		'lastEditor',
		'lastEditionDate',
		'toUser',
		'read',
		'status'
	);

	public $memberCreateAttributes = array(
		'content' => 'content',
		'toUser' => 'toUser'
	);
}