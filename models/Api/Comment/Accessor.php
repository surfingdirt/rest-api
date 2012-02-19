<?php
class Api_Comment_Accessor extends Api_Data_Accessor
{
    protected $_disregardUpdates = array(
        'tags',
        'submit',
        'skipAutoFields',
        'longitude',
        'latitude',
        'zoom',
        'yaw',
        'pitch',
        'mapType',
    	'locationFlag'
    );	
	
	public $publicReadAttributes = array(
		'id',
		'content',
		'date',
		'submitter',
		'lastEditor',
		'lastEditionDate',
		'status',
		'tone',
	);

	public $memberCreateAttributes = array(
		'content' => 'content',
		'tone' => 'tone',
		'itemId' => 'parentId',
		'itemType' => 'parentType',
	);
    
	public $ownWriteAttributes = array(
		'content' => 'content',
		'tone' => 'tone',
		'itemId' => 'parentId',
		'itemType' => 'parentType',
	);
	
	public $adminWriteAttributes = array(
		'status' => 'status',
	);    
}