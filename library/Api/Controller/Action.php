<?php
abstract class Api_Controller_Action extends Zend_Controller_Action
{
    /**
     * Name of the resource
     * @var string
     */
	protected $_resourceName = null;

	/**
	 * @var Api_Data_Accessor
	 */
	protected $_accessor = null;

	public $listStart = 0;

	public $listCount = 12;

	public $listDir = 'ASC';

	/**
	 * The default sorting key
	 * @var string
	 */
	public $listKey = 'id';

	public function init()
	{
		parent::init();

		$accept = $this->getRequest()->getHeader('accept');
		$headers = apache_request_headers();
		$this->_setupViewPath($accept, $headers);

		$userTable = new Api_User();
		$userId = isset($_SESSION[User::COLUMN_USERID]) ? $_SESSION[User::COLUMN_USERID] : 0;
		//$userId = 1;
       	$results = $userTable->find($userId);
	    if($results && $user = $results->current()){
       		Globals::setUser($user);
		} else {
			throw new Exception("Could not find user '$userId'");
		}

		$this->_user = $user;
		
		Zend_Registry::set('Zend_Translate', Globals::getTranslate());
		
		$this->_acl = Globals::getAcl();
		list($this->_table, $this->_accessor) = $this->_mapResource($this->_request->getControllerName());

		$_SESSION[User::COLUMN_USERID] = $user->{User::COLUMN_USERID};
	}
	
	protected function _setupViewPath($accept, $headers)
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setViewScriptPathSpec(':action.json');
		$this->getResponse()->setRawHeader('Content-Type: application/json; charset=UTF-8');
	} 

	//-----------------------------------------------------------------------------------------------------------------
    // LIST
	//-----------------------------------------------------------------------------------------------------------------
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function listAction()
    {
    	$count = $this->getRequest()->getParam('count', $this->listCount);
    	$start = $this->getRequest()->getParam('start', $this->listStart);

    	$dir = $this->getRequest()->getParam('dir', $this->listDir);
    	$dir = ($dir == 'desc') ? 'DESC':'ASC';

    	$sort = $this->getRequest()->getParam('sort', $this->listKey);
		$sort = in_array($sort, array('userId', 'username')) ? $sort:$this->listKey;

    	$where = $this->_getWhereClause($this->_user);

		$results = $this->_getAllObjects($where, $sort, $dir, $count, $start);

		$resources = array();
		foreach($results as $object){
			$resources[] = $this->_accessor->getObjectData($object, $this->_request->getActionName());
		}

		$this->view->resources = $resources;
    }

   	//-----------------------------------------------------------------------------------------------------------------
    // GET
	//-----------------------------------------------------------------------------------------------------------------
    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
    	$id = $this->_request->getParam('id');
    	$result = $this->_table->find($id);
    	if(empty($result) || !$object = $result->current()){
			throw new Api_Exception_NotFound();
    	}

   		$this->view->resource = $this->_accessor->getObjectData($object, $this->_request->getActionName(), $this->_request->getParams());
    }

	//-----------------------------------------------------------------------------------------------------------------
    // POST
	//-----------------------------------------------------------------------------------------------------------------
	/**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
    	$object = $this->_table->fetchNew();
    	if(!$object->isCreatableBy($this->_user, $this->_acl)){
    		throw new Api_Exception_Unauthorised();
    	}

    	$data = $this->_request->getPost();

    	$this->view->resourceId = null;
    	$this->view->errors = array();

   		$this->_preObjectCreation($object, $data);
   		list($id, $object, $this->view->errors) = $this->_accessor->createObjectWithData($object, $data);
   		if($id){
   			$this->_postObjectCreation($object, $data);
   		}

    	$this->view->resourceId = $object->getId();
    }

    protected function _preObjectCreation($object, $data)
    {
    	// Do nothing by default
    }

    protected function _cleanUpAfterCreationFailure($object, $data)
    {
    }

    protected function _postObjectCreation($object, $data)
    {
    	// Do nothing by default
    }

	//-----------------------------------------------------------------------------------------------------------------
    // PUT
	//-----------------------------------------------------------------------------------------------------------------
    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
    	$id = $this->_request->getParam('id');
		$data = $this->_getPut();

    	$result = $this->_table->find($id);
        if(empty($result) || !$object = $result->current()){
			throw new Api_Exception_NotFound();
    	}
    	if(!$object->isEditableBy($this->_user, $this->_acl)){
    		throw new Api_Exception_Unauthorised();
    	}

   		$this->_preObjectUpdate($object, $data);
   		$errors = $this->_accessor->updateObjectWithData($object, $data);
		if(empty($errors)) {
			$this->_postObjectUpdate($object, $data);
		}
		$this->view->errors = $errors;
    	$this->view->resourceId = $id;
    }

    protected function _getPut()
    {
    	$data = $this->_request->getParams();
		unset($data['module']);
		unset($data['controller']);
		unset($data['action']);
		unset($data['id']);

		//error_log('PUT '.var_export($data, TRUE));

		return $data;
    }

    protected function _preObjectUpdate($object, $data)
    {
    }

    protected function _postObjectUpdate($object, $data)
    {
    }

    //-----------------------------------------------------------------------------------------------------------------
    // DELETE
	//-----------------------------------------------------------------------------------------------------------------
    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
    	$id = $this->_request->getParam('id');

    	$result = $this->_table->find($id);
        if(empty($result) || !$object = $result->current()){
			throw new Api_Exception_NotFound();
    	}
    	if(!$object->isDeletableBy($this->_user, $this->_acl)){
    		throw new Api_Exception_Unauthorised();
    	}

   		$this->_preObjectDelete($object);
		if($status = $this->_accessor->deleteObject($object)) {
			$this->_postObjectDelete($object);
		}
		$this->view->status = $status;
    	$this->view->resourceId = $id;
    }

    protected function _preObjectDelete($object)
    {
    	$object->clearCache();
    }

    protected function _postObjectDelete($object)
    {
    	
    }

	//-----------------------------------------------------------------------------------------------------------------
    // MISC FUNCTIONS
	//-----------------------------------------------------------------------------------------------------------------
	/**
	 * Returns the name of the table that corresponds to the resource in the url
	 * @param string $controllerName
	 */
	protected function _mapResource($key)
	{
		$resources = array(
			'albums' => 'Album',
			'riders-albums' => 'Album',
			'mediaalbum' => 'Album',
		
			'checkins' => 'Checkin',
			
			'comments' => 'Comment',
		
			'countries' => 'Country',
		
			'regions' => 'Dpt',
		
			'notifications' => 'Item',
			'locations' => 'Item',

			'media' => 'Media',
			'photo' => 'Media',
			'video' => 'Media',
		
			'messages' => 'PrivateMessage',
			'privatemessage' => 'PrivateMessage', 
			
			'spots' => 'Spot',
			'spot' => 'Spot',
		
			'tricks' => 'Trick',
			'trick' => 'Trick',
			
			'riders' => 'User',
			'user' => 'User',
		);

		if(!isset($resources[$key])){
			throw new Exception('Unknown resource');
		}

		$resourceName = 'Api_'.$resources[$key];
		$accessorName = $resourceName.'_Accessor';

		$table = new $resourceName();
		$accessor = new $accessorName($this->_user, $this->_acl);

		return array($table, $accessor);
	}

	/**
	 * 
	 * Builds an array of object matching the criteria
	 * @param conditions $where
	 * @param sort criteria $sort
	 * @param sort direction $dir
	 * @param number of objects to return $count
	 * @param index of the first object to return $start
	 */
	protected function _getAllObjects($where, $sort = null, $dir = null, $count = null, $start = null)
	{
		if(!$sort && !$dir) {
			$order = null;
		} else {
			$order = $sort.' '.$dir;
		}
		$results = $this->_table->fetchAll($where, $order, $count, $start);
		return $results;
	}
	
    protected function _getWhereClause(User_Row $user)
    {
		if(in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))){
			$return = '1';
		} else {
			$return = $this->_table->getAdapter()->quoteInto('status = ?', Data::VALID);
		}

		return $return;
    }

}