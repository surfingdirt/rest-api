<?php

class Lib_Controller_Helper_Emailer extends Zend_Controller_Action_Helper_Abstract
{
  // Email types
  const REGISTRATION_EMAIL = 0;
  const LOST_PASSWORD_EMAIL = 1;
  const CONTACT_EMAIL = 2;

  public function direct()
  {
    return $this;
  }

  /**
   * Sends an email to a user
   *
   * @param string $to
   * @param array $params
   * @param string $type
   * @return boolean
   */
  public function sendEmail($to, $params = array(), $type = self::REGISTRATION_EMAIL)
  {
    $view = new Zend_View();
    $view->addHelperPath('../library/Lib/View/Helper/', 'Lib_View_Helper');
    $appName = Globals::getAppName();
    $view->setScriptPath("../application/views/scripts/");

    foreach ($params as $name => $value) {
      $view->$name = $value;
    }

    switch ($type) {
      case self::LOST_PASSWORD_EMAIL:
        $fileName = 'users/email/lostPassword.phtml';
        $fileNameTxt = 'users/email/lostPasswordTxt.phtml';
        $subject = ucfirst(Globals::getTranslate()->_('lostPasswordEmailTitle'));
        break;
      case self::REGISTRATION_EMAIL:
        $fileName = 'users/email/creationConfirmation.phtml';
        $fileNameTxt = 'users/email/creationConfirmationTxt.phtml';
        $subject = ucfirst(Globals::getTranslate()->_('subscriptionEmailTitle'));
        break;
      default:
        throw new Lib_Exception('Unknown email type - "' . $type . '"');
        break;
    }


    // TODO: recreate view scripts for all stages
    // TODO: replace Zend_Mail with a call to Mailgun
//    $emailContent = $view->render($fileName);
//    $emailContentTxt = $view->render($fileNameTxt);
    $emailContent = <<<HTML
    <ul>
      <li>userId: {$params['userId']}</li>
      <li>activationKey: {$params['activationKey']}</li>
    </ul>
HTML;

    $emailContentTxt = <<<TXT
      userId: {$params['userId']}
      activationKey: {$params['activationKey']}
TXT;


    if (APPLICATION_ENV == 'development') {
//      return true;
    }

    try {
      $mail = new Zend_Mail(APP_PAGE_ENCODING);
      $mail->setFrom(EMAIL_FROM, APP_EMAIL_FROM_STRING);
      $mail->setSubject($subject);
      $mail->addTo(strtolower($to));
      $mail->setBodyHtml($emailContent);
      $mail->setBodyText($emailContentTxt);
      $emailStatus = $mail->send();
    } catch (Zend_Mail_Transport_Exception $e) {
      $msg = "Email error" . PHP_EOL . $e->getMessage();
      Globals::getLogger()->emailError($msg);
      $emailStatus = false;
    }

    return $emailStatus;
  }
}