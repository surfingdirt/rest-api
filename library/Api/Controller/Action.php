<?php

abstract class Api_Controller_Action extends Zend_Controller_Action
{
  /**
   * The error string for when an immutable field is being updated.
   */
  const IMMUTABLE = 'immutable';

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
  public $listKey = 'date';
  public $allowedListKeys = array('userId', 'username');

  public function init()
  {
    parent::init();

    $this->_setupViewPath();

    $userTable = new Api_User();

    try {
      $userId = Lib_JWT::setup($this->getRequest(), JWT_SECRET);
    } catch (Lib_JWT_Exception $e) {
      throw $e;
    }

    $results = $userTable->find($userId);
    if ($results && $user = $results->current()) {
      Globals::setUser($user);
      $this->_user = $user;
    } else {
      throw new Exception("Could not find user '$userId'");
    }

    Zend_Registry::set('Zend_Translate', Globals::getTranslate());

    $this->_acl = Globals::getAcl();
    list($this->_table, $this->_accessor) = $this->_mapResource($this->_request->getControllerName());
  }

  protected function _setupViewPath()
  {
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setViewScriptPathSpec('view.phtml');
    $this->getResponse()->setRawHeader('Content-Type: application/json; charset=UTF-8');
  }

  /**
   * Returns all user-land parameters.
   * @return array
   * @throws Zend_Controller_Action_Exception
   */
  protected function _getBodyParams()
  {
    $params = $this->_request->getParams();

    unset($params[$this->_request->getControllerKey()]);
    unset($params[$this->_request->getActionKey()]);
    unset($params[$this->_request->getModuleKey()]);
    unset($params['id']);

    return $params;
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

    $dir = $this->_getDir();
    $sort = $this->_getSort();
    $where = $this->_getWhereClause($this->_user);

    $results = $this->_getAllObjects($where, $sort, $dir, $count, $start);

    $resources = array();
    foreach ($results as $object) {
      $resources[] = $this->_accessor->getObjectData($object, $this->_request->getActionName(), $this->_request->getParams());
    }

    $this->view->output = $resources;
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
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $result = $this->_table->find($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }

    if (!$object->isReadableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $this->view->output = $this->_accessor->getObjectData(
      $object,
      $this->_request->getActionName(),
      $this->_request->getParams()
    );
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
    if (!$object->isCreatableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $data = $this->_getBodyParams();
    try {
      $this->_preObjectCreation($object, $data);
      list($id, $object, $errors) = $this->_accessor->createObjectWithData($object, $data);
    } catch (Lib_Exception $e) {
      $errors = array($e->getMessage());
    }
    if ($id) {
      $this->_postObjectCreation($object, $data);
    }
    if ($errors) {
      $this->getResponse()->setRawHeader('HTTP/1.1 400 Bad Request');
      $this->view->output = array('errors' => $errors, 'code' => Api_ErrorCodes::FORM_BAD_INPUT);
    } else {
      $this->view->output = $this->_accessor->getObjectData(
        $object,
        'get',
        null
      );
    }
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
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $data = $this->_getBodyParams();

    // Force data load from the disk to guarantee we get the latest version
    // and avoid nasty bug with cache where data would be serialized as one type and expected as something else on the way out
    $result = $this->_table->findWithoutCache($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }
    if (!$object->isEditableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $errors = $this->_preObjectUpdate($object, $data);
    if (!$errors) {
      $errors = $this->_accessor->updateObjectWithData($object, $data);
    }
    if (empty($errors)) {
      $this->_postObjectUpdate($object, $data);
      $this->view->output = $this->_accessor->getObjectData(
        $object,
        'get',
        null
      );

    } else {
      $this->_badRequest();
      $this->view->output = array('errors' => $errors, 'code' => Api_ErrorCodes::FORM_BAD_INPUT);
    }
  }

  protected function _preObjectUpdate($object, $data)
  {
    $errors = array();
    foreach ($this->_accessor->forbiddenWriteAttributes as $formKey => $dbKey) {
      if (isset($data[$formKey]) && $object->$dbKey != $data[$formKey]) {
        $errors[$formKey] = array(self::IMMUTABLE);
      }
    }
    return $errors;
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
    if (!$id) {
      throw new Api_Exception_BadRequest();
    }
    $result = $this->_table->find($id);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }
    if (!$object->isDeletableBy($this->_user, $this->_acl)) {
      throw new Api_Exception_Unauthorised();
    }

    $this->_preObjectDelete($object);
    if ($status = $this->_accessor->deleteObject($object)) {
      $this->_postObjectDelete($object);
    }
    $this->view->output = array('status' => $status);
  }

  protected function _preObjectDelete($object)
  {
    $object->clearCache();
  }

  protected function _postObjectDelete($object)
  {

  }

  //-----------------------------------------------------------------------------------------------------------------
  // OPTIONS
  //-----------------------------------------------------------------------------------------------------------------
  public function optionsAction()
  {
    $this->getResponse()->setHeader(
      'Access-Control-Allow-Methods',
      'OPTIONS, INDEX, GET, POST, PUT, DELETE'
    );
  }

  //-----------------------------------------------------------------------------------------------------------------
  // ERROR HEADERS
  //-----------------------------------------------------------------------------------------------------------------
  protected function _notFound()
  {
    $this->_error('HTTP/1.1 404 Not Found');
  }

  protected function _forbidden()
  {
    $this->_error('HTTP/1.1 403 Forbidden');
  }

  protected function _badRequest()
  {
    $this->_error('HTTP/1.1 400 Bad Request');
  }

  protected function _genericError($message)
  {
    $this->_error('HTTP/1.1 500 Internal Server Error');
  }

  protected function _error($rawHeader)
  {
    $this->getResponse()->setRawHeader($rawHeader);
  }

  //-----------------------------------------------------------------------------------------------------------------
  // MISC FUNCTIONS
  //-----------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of the table that corresponds to the resource in the url
   * @param string $key
   * @return array
   * @throws Exception
   */
  protected function _mapResource($key)
  {
    $resourceName = $this->_getResourceName($key);
    $accessorName = $resourceName . '_Accessor';

    $table = new $resourceName();
    $accessor = new $accessorName($this->_user, $this->_acl);

    return array($table, $accessor);
  }

  protected function _getResources() {
    return array(
      'album' => 'Album',
      'comments' => 'Comment',
      'comment' => 'Comment',
      'feed' => 'Feed',
      'media' => 'Media',
      'mediaalbum' => 'Album',
      'notifications' => 'Notification',
      'photo' => 'Media',
      'user' => 'User',
      'useralbum' => 'Album',
      'video' => 'Media',
    );
  }

  protected function _getResourceTranslatedFields($itemType) {
    $fieldsPerType = array(
      'album' => ['title', 'description'],
      'comments' => ['content'],
      'media' => ['title', 'description'],
      'user' => ['bio'],
    );
    return $fieldsPerType[$itemType];
  }

  protected function _getResourceName($key) {
    $resources = $this->_getResources();
    if (!isset($resources[$key])) {
      throw new Api_Exception_BadRequest('Unknown resource');
    }
    return 'Api_' . $resources[$key];
  }

  /**
   * Builds an array of object matching the criteria
   * @param conditions $where
   * @param sort criteria $sort
   * @param sort direction $dir
   * @param number of objects to return $count
   * @param index of the first object to return $start
   */
  protected function _getAllObjects($where, $sort = null, $dir = null, $count = null, $start = null)
  {
    if (!$sort && !$dir) {
      $order = null;
    } else {
      $order = $sort . ' ' . $dir;
    }
    $results = $this->_table->fetchAll($where, $order, $count, $start);
    return $results;
  }

  protected function _getWhereClause(User_Row $user)
  {
    if (in_array($user->status, array(User::STATUS_EDITOR, User::STATUS_ADMIN))) {
      $return = '1';
    } else {
      $return = $this->_table->getAdapter()->quoteInto('status = ?', Data::VALID);
    }

    return $return;
  }

  protected function _getDir()
  {
    $dir = $this->getRequest()->getParam('dir', $this->listDir);
    $dir = ($dir == 'desc') ? 'DESC' : 'ASC';
    return $dir;
  }

  protected function _getSort()
  {
    $sort = $this->getRequest()->getParam('sort', $this->listKey);
    $sort = in_array($sort, $this->allowedListKeys) ? $sort : $this->listKey;
    return $sort;
  }


}