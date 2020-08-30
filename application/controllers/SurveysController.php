<?php
class SurveysController extends Api_Controller_Action
{
  public $listDir = 'DESC';

  public function getVoteAction()
  {
    if (!$this->_user->isLoggedIn()) {
      return $this->_forbidden();
    }

    $surveyId = $this->_request->getParam('surveyId');
    $db = Globals::getMainDatabase();

    $quote  = $db->quoteInto("(userId = ?", $this->_user->getId());
    $quote .= $db->quoteInto(" AND surveyId = ?)", $surveyId);
    $sql = "SELECT * FROM survey_answers WHERE $quote";
    $stmt = $db->query($sql);
    $data = $stmt->fetch();

    $this->view->output = ['choice' => $data ? $data['choice'] : null];
  }

  public function castVoteAction()
  {
    if (!$this->_user->isLoggedIn()) {
      return $this->_forbidden();
    }

    if ($this->_request->getMethod() != 'POST') {
      throw new Api_Exception_BadRequest();
    }

    $db = Globals::getMainDatabase();

    $surveyId = $db->quote($this->_request->getParam('surveyId'));
    $userId = $db->quote($this->_user->getId());
    $choice = $this->_request->getParam('choice');

    if (strlen($choice) == 0) {
      $quote  = $db->quoteInto("(userId = ?", $this->_user->getId());
      $quote .= " AND surveyId = $surveyId)";
      $sql = "DELETE FROM survey_answers WHERE $quote";
      $choice = null;

    } else {
      $choice = $db->quote($choice);
      $sql = "REPLACE INTO survey_answers (surveyId, userId, choice) VALUES ($surveyId, $userId, $choice)";
    }

    $success = true;
    try {
      $db->query($sql);
    } catch (Exception $e) {
      $success = false;
    }

    $this->view->output = ['success' => $success, 'surveyId' => $surveyId, 'choice' => $choice];
  }

  protected function _mapResource($key) {
    // Do nothing!
  }
}