<?php
class Api_Media_Row extends Media_Item_Row
{
	/**
	 * This array contains the list of attributes that
	 * should not be sent to a client.
	 * @var array
	 */
	protected $_hiddenFromClientAttr = array(

	);

	/**
	 * This method returns the data that is 'public',
	 * that is to say, visible to the client.
	 */
	public function getDataForClient()
	{
		$ret = array();
		foreach($this->_data as $name => $value){
			if(in_array($name, $this->_hiddenFromClientAttr)){
				continue;
			}

			$ret[$name] = $value;
		}
		return $ret;
	}
	
	public function getForm(User_Row $user, Lib_Acl $acl, $options = null, $data = null)
	{
		if(isset($data['mediaType']) && $data['mediaType'] == Media_Item::TYPE_VIDEO){
			$form = new Api_Media_Form_Video($this, $user, $acl);
		} else {
			$form = new Api_Media_Form_Photo($this, $user, $acl);
		}
		
		return $form;
	}
	
	public function getItemType()
	{
		if($this->mediaType == Media_Item::TYPE_VIDEO) {
			return Media_Item::TYPE_VIDEO;
		} else {
			 return Media_Item::TYPE_PHOTO;
		}
	}	
}