<?php

abstract class Media_Item_Form extends Data_Form
{
  /**
   * Disabled because it is buggy: albums cannot be found on submit
   * @todo: FIX IT !!!!!
   */
  const ALLOW_ALBUM_CHANGE = false;

  /**
   * Whether the media is required or not on form submit
   *
   * @var boolean
   */
  protected $_mediaRequired = true;

  /**
   * Constructor
   *
   * @param Data_Row $object
   * @param User_Row $user
   * @param Lib_Acl $acl
   * @param array $options
   */
  public function __construct(Data_Row $object, User_Row $user, Lib_Acl $acl, $options = null)
  {
    if ($object->id) {
      $this->_mediaRequired = false;
    }
    parent::__construct($object, $user, $acl, $options);
  }

  protected function _setup()
  {

    $isEditor = $this->_acl->isAllowed($this->_user, Lib_Acl::EDITOR_RESOURCE);
    $editing = !empty($this->_object->id);
    $elements = $this->_buildElements();

    if ($editing) {
      // New post: we can decide to keep this hidden by specifying 'invalid' on submit
      $elements['status'] = $this->getStatus();

      if ($isEditor) {
        $elements = array_merge($elements, array(
          'skipAutoFields' => $this->getSkipAutoFields(),
          'submitter' => $this->getSubmitter(),
          'date' => $this->getDate(),
          'lastEditionDate' => $this->getLastEditionDate(),
          'lastEditor' => $this->getLastEditor(),
        ));
      }
    }
    $this->addElements($elements);
  }

  protected function _buildElements()
  {
    $isEditor = $this->_acl->isAllowed($this->_user, Lib_Acl::EDITOR_RESOURCE);

    $elements = array(
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
      'storageType' => $this->getStorageType(),
      'mediaSubType' => $this->getMediaSubType(),
      'users' => $this->getUsers(),
    );
    if (!$this->_object->id || $isEditor && $this->_object->id && self::ALLOW_ALBUM_CHANGE) {
      // Editors can move a media to a different album
      $elements['albumId'] = $this->getAlbumId();
    }

    return $elements;
  }

  public function getUsers()
  {
    $element = new Lib_Form_Element_Users('users', $this->_object);
    $element->setLabel(ucfirst(Globals::getTranslate()->_('users')))
      ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
      ->addFilter('HTMLPurifier');
    return $element;
  }

  public function getAlbumId($required = true)
  {
    $element = new Lib_Form_Element_Album_Id('albumId');
    $element->setRequired($required);
    return $element;
  }

  public function getStorageType($required = true)
  {
    $element = new Lib_Form_Element_Media_StorageType('storageType');
    $element
      ->addValidator(new Lib_Form_Element_Media_StorageType_Validate());

    return $element;
  }
}