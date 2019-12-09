<?php

class User_Form_Update extends Lib_Form
{
  /**
   * Is user confirmed yet ?
   *
   * @var boolean
   */
  protected $_pending;
  /**
   * User being edited
   * @var User_Row
   */
  protected $_user;

  /**
   * Constructor
   *
   * @param boolean $pending
   * @param boolean $useOpenId
   * @param booleanarray $options
   */
  public function __construct(User_Row $user, $pending = false, $options = null)
  {
    $this->_pending = $pending;
    $this->_user = $user;

    parent::__construct($options);

    // TODO: handle language and email updates!
//    // These elements are only accessible after account confirmation
//      $languages = $this->_translator->getList();
//      $lang = new Zend_Form_Element_Select('lang');
//      $lang->setLabel(ucfirst(Globals::getTranslate()->_('language')))
//        ->setMultiOptions($languages);

    if (!$this->_pending) {
      $passwordOld = new Lib_Form_Element_Password_Old();
      $password = new Lib_Form_Element_Password();
      $passwordConfirm = new Lib_Form_Element_Password_Confirm(false, $this, $password->getName());
      $this->addElements(array($passwordOld, $password, $passwordConfirm));
    }
  }

  /**
   * This method allows to check passwords as well as other
   * elements.
   *
   * @param array $data
   * @return boolean
   */
  public function isUpdateValid($data, $user)
  {
    $valid = parent::isValid($data);

    $hasErrors = false;
    if (!empty($data[User::INPUT_PASSWORD_OLD])) {
      /**
       * Something was typed in the old password field
       */
      if ($user->{User::COLUMN_PASSWORD} != md5($data[User::INPUT_PASSWORD_OLD])) {
        /**
         * Old password is incorrect, stop right here
         */
        $this->getElement(User::INPUT_PASSWORD_OLD)->clearErrorMessages()->addError('wrongPassword');
        $hasErrors = true;
      } else {
        /**
         * Old password is correct, check for updates in new password fields
         */
        if ($data[User::INPUT_PASSWORD_CONFIRM] !== $data[User::INPUT_PASSWORD]) {
          $this->getElement(User::INPUT_PASSWORD)->clearErrorMessages();
          $this->getElement(User::INPUT_PASSWORD_CONFIRM)->clearErrorMessages()
            ->addError(Zend_Validate_Identical::NOT_SAME);
          $hasErrors = true;
        }
      }
    } else {
      /**
       * Nothing was typed in the old password field
       */
      if (!empty($data[User::INPUT_PASSWORD_CONFIRM]) || !empty($data[User::INPUT_PASSWORD])) {
        /**
         * New passwords were given, but we need the old one !
         */
        $this->getElement(User::INPUT_PASSWORD_OLD)->clearErrorMessages()->addError('isEmpty');
        $hasErrors = true;
      }
    }

    $valid = $valid && !$hasErrors;
    return $valid;
  }

  /**
   * Call special formatting functions before populating
   * form with data form database
   *
   * @param array $data
   */
  public function populateFromDatabaseData(array $data)
  {
    $formattedData = $data;
    $elements = $this->getElements();

    foreach ($elements as $name => $element) {
      if (method_exists($element, 'getValueFromDatabase')) {
        $rawValue = isset($data[$name]) ? $data[$name] : null;
        $formattedData[$name] = $element->getValueFromDatabase($rawValue);
      }
    }

    $this->populate($formattedData);
    return $formattedData;
  }

  /**
   * Call special formatting function before storing data
   * into database
   *
   * @param array $data
   * @return array
   */
  public function getFormattedValuesForDatabase()
  {
    $formattedData = parent::getFormattedValuesForDatabase();
    if (!empty($formattedData['avatarUrl'])) {
      $formattedData['avatar'] = $formattedData['avatarUrl'];
    }

    unset($formattedData['avatarUrl']);

    return $formattedData;
  }

  /**
   * Factory for longitude element
   *
   * @return Lib_Form_Element_Location_Angle_Longitude
   */
  public static function getLongitude()
  {
    $element = new Lib_Form_Element_Location_Angle_Longitude();
    return $element;
  }

  /**
   * Factory for latitude element
   *
   * @return Lib_Form_Element_Location_Angle_Latitude
   */
  public static function getLatitude()
  {
    $element = new Lib_Form_Element_Location_Angle_Latitude();
    return $element;
  }

  /**
   * Factory for zoom element
   *
   * @return Lib_Form_Element_Location_Zoom
   */
  public static function getZoom()
  {
    $element = new Lib_Form_Element_Location_Zoom();
    return $element;
  }

  /**
   * Factory for map type element
   *
   * @return Lib_Form_Element_Location_MapType
   */
  public static function getMapType()
  {
    $element = new Lib_Form_Element_Location_MapType();
    return $element;
  }

  /**
   * Factory for yaw element
   *
   * @return Lib_Form_Element_Location_Angle_Yaw
   */
  public static function getYaw()
  {
    $element = new Lib_Form_Element_Location_Angle_Yaw();
    return $element;
  }

  /**
   * Factory for pitch element
   *
   * @return Lib_Form_Element_Location_Angle_Pitch
   */
  public static function getPitch()
  {
    $element = new Lib_Form_Element_Location_Angle_Pitch();
    return $element;
  }

  public function getData()
  {
    return $this->_object;
  }
}