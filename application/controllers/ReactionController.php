<?php
class ReactionController extends Api_Controller_Action
{
  public $listDir = 'DESC';

  public function getAction()
  {
    $this->_noPutOrGet();
  }

  public function putAction()
  {
    $this->_noPutOrGet();
  }

  protected function _noPutOrGet()
  {
    $this->getResponse()->setRawHeader('HTTP/1.1 400 Bad Request');
    $this->view->output = array('code' => Api_ErrorCodes::BAD_METHOD);
  }

  public function listAction()
  {
    if (!$this->_user->isLoggedIn()) {
      $this->_forbidden();
    }

    parent::listAction();
  }

  protected function _getWhereClause(User_Row $user)
  {
    $return = $this->_table->getAdapter()->quoteInto('submitter = ?', $this->_user->getId());
    return $return;
  }
}