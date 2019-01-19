<?php
class ImageController extends Lib_Rest_Controller
{
  public function init()
  {
    parent::init();
    if(!Globals::getJWT()) {
      $this->_unauthorised();
    }
  }

  public function postAction()
  {
    // Handle image POST requests like in mountainboard.fr, avec plusieurs tailles et avec une destination en fonction
    // du type d'image a sauver.
  }

  public function deleteAction()
  {
    // Only owner and editor/admin have access
  }

  /*
   * All other methods are forbiddem
   */

  public function indexAction()
  {
    $this->_unauthorised();
  }

  public function listAction()
  {
    $this->_unauthorised();
  }

  public function putAction()
  {
    $this->_unauthorised();
  }

  public function getAction()
  {
    $this->_unauthorised();
  }
}