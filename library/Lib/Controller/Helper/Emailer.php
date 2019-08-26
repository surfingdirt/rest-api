<?php
use Mailgun\Mailgun;

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
    $appName = Globals::getAppName();

    try {
      $mail = Mailgun::create(MAILER_API_KEY, MAILER_API_DOMAIN);

      switch ($type) {
        case self::LOST_PASSWORD_EMAIL:
          $subject = ucfirst(Globals::getTranslate()->_('lostPasswordEmailTitle'));
          $template = 'lost-password';
          $variables = json_encode(array(
          ));
          break;
        case self::REGISTRATION_EMAIL:
          $subject = ucfirst(Globals::getTranslate()->_('subscriptionEmailTitle'));
          $template = 'confirm-email';
          // TODO: translate variables
          $variables = json_encode(array(
            "title" => "Confirm your email address",
            "hello" => "Hello",
            "username" => $params['username'],
            "confirmEmail" => "Welcome to the site! Before you can continue, please confirm your email address by clicking the link below:",
            "destination" => APP_URL.'/confirm-email?key='.$params['activationKey'],
            "confirmButtonLabel" => "Confirm my email address",
            "senderSignature" => $appName,
            "siteUrl" => APP_URL,
          ));
          break;
        default:
          throw new Lib_Exception('Unknown email type - "' . $type . '"');
          break;
      }

      @$mail->messages()->send(MAILER_DOMAIN, [
        'from'    => EMAIL_FROM_STRING,
        'to'      => strtolower($to),
        'subject' => $subject,
        'template' => $template,
        'inline' => [
          array('filePath' => BASE_PATH . EMAIL_LOGO_IMAGE, 'filename' => 'email-logo.png'),
        ],
        'h:X-Mailgun-Variables' => $variables,
      ]);
    } catch (Exception $e) {
      $msg = "Email error" . PHP_EOL . $e->getMessage();
      Globals::getLogger()->emailError($msg);
      $emailStatus = false;
    }

    return $emailStatus;
  }
}