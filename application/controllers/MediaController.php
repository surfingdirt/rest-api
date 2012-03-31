<?php
class MediaController extends Api_Controller_Action
{
	public function listAction()
	{
		throw new Api_Exception_Unauthorised();
	}

    public function postAction()
    {
    	$data = $this->_request->getPost();
    	if(!isset($data['mediaType']) || $data['mediaType'] != Media_Item::TYPE_PHOTO) {
			$this->_table = new Api_Media_Video();
		} else {
			$this->_table = new Api_Media_Photo();
		}    	
    	
    	$object = $this->_table->fetchNew();
    	if(!$object->isCreatableBy($this->_user, $this->_acl)){
    		throw new Api_Exception_Unauthorised();
    	}

    	$this->view->errors = array();

   		$this->_preObjectCreation($object, $data);
   		list($id, $this->view->errors) = $this->_accessor->createObjectWithData($object, $data);
   		if($id){
   			$this->_postObjectCreation($object, $data);
   		}
   		if($this->view->errors) {
   			$this->getResponse()->setRawHeader('HTTP/1.1 400 Bad Request');
   		}

    	$this->view->resourceId = $object->getId();
    }
    
    public function putAction()
    {
    	$id = $this->_request->getParam('id');
		$data = $this->_getPut();

    	$result = $this->_table->find($id);
        if(empty($result) || !$object = $result->current()){
			throw new Api_Exception_NotFound();
    	}
    	
    	$object = Media_Item_Factory::buildItem($object->id, $object->mediaType);    	
    	if(!$object->isEditableBy($this->_user, $this->_acl)){
    		throw new Api_Exception_Unauthorised();
    	}

   		$this->_preObjectUpdate($object, $data);
   		$errors = $this->_accessor->updateObjectWithData($object, $data);
		if(empty($errors)) {
			$this->_postObjectUpdate($object, $data);
		} else {
   			$this->getResponse()->setRawHeader('HTTP/1.1 400 Bad Request');
		}
		$this->view->errors = $errors;
    	$this->view->resourceId = $id;
    }    
    
    protected function _preObjectCreation($object, $data)
    {
     	if(!isset($data['mediaType']) || $data['mediaType'] != Media_Item::TYPE_PHOTO) {
			$object->mediaType = Media_Item::TYPE_VIDEO;
		} else {
			$object->mediaType = Media_Item::TYPE_PHOTO;
		}      
    }
}