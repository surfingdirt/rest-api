<?php
class TranslationController extends Api_Controller_Action
{
  public function init()
  {
    $this->_setupViewPath();
  }

  public function indexAction()
  {
    $method = $this->_request->getMethod();
    if (!in_array($method, ['POST', 'PUT'])) {
      throw new Api_Exception_BadRequest();
    }

    $itemType = $this->_request->getParam('itemType');
    $itemId = $this->_request->getParam('itemId');
    $field = $this->_request->getParam('field');

    $payload = $this->_request->getParam($field);
    $locale = $payload['locale'];
    $text = $payload['text'];

    $newTranslation = [
      'locale' => $locale, 'text' => $text
    ];

    // Build translated object and translations
    $resourceName = $this->_getResourceName($itemType);
    $table = new $resourceName();
    $result = $table->find($itemId);
    if (empty($result) || !$object = $result->current()) {
      throw new Api_Exception_NotFound();
    }
    $existingTranslations = Lib_Translate::decodeField($object->$field);

    // Empty value is allowed for PUT because it means "remove"
    $textMustNotBeEmpty = $method !== 'PUT';
    $validator = new Lib_Validate_Translated($textMustNotBeEmpty);
    $newTranslations = null;

    if ($method === 'POST') {
      if(!$validator->isValid([$newTranslation])) {
        $errors = $validator->getErrors();
        $this->_badRequest();
        $this->view->output = array('errors' => $errors, 'code' => Api_ErrorCodes::FORM_BAD_INPUT);
        return;
      }
      // Add - but check for existing entry
      if ($this->_getEntryIndexForLocale($existingTranslations, $locale) !== false) {
        $this->_badRequest();
        $this->view->output = array('errors' => ['localeExists'], 'code' => Api_ErrorCodes::FORM_BAD_INPUT);
        return;
      }
      $newTranslations = array_merge($existingTranslations, [$newTranslation]);

    } else {
      $index = $this->_getEntryIndexForLocale($existingTranslations, $locale);
      if ($text === null) {
        if ($index !== false) {
          // Remove
          unset($existingTranslations[$index]);
          $newTranslations = $existingTranslations;
        } else {
          // Entry does not already exist - do nothing!
        }
      } else {
        if(!$validator->isValid([$newTranslation])) {
          $errors = $validator->getErrors();
          $this->_badRequest();
          $this->view->output = array('errors' => $errors, 'code' => Api_ErrorCodes::FORM_BAD_INPUT);
          return;
        }

        if ($index !== false) {
          // Modify
          $existingTranslations[$index] = $newTranslation;
          $newTranslations = $existingTranslations;
        } else {
          // Error: this should have been a POST request
          $this->_badRequest();
          $this->view->output = array('errors' => $errors, 'code' => Api_ErrorCodes::BAD_METHOD);
          return;
        }
      }
    }

    $object->$field = Lib_Translate::encodeField($newTranslations);
    // Do not update edition fields
    $object->save(true);

    $this->view->output = $newTranslations;
  }

  public function putAction()
  {
    $itemType = $this->_request->getParam('itemType');
    $itemId = $this->_request->getParam('itemId');
    $field = $this->_request->getParam('field');

    $output = [
      'itemType' => $itemType,
      'itemId' => $itemId,
      'field' => $field,
    ];
    $this->view->output = $output;
  }

  public function getAction()
  {
    // GET doesn't serve any purpose: data is accessed directly through items
    throw new Api_Exception_BadRequest();
  }

  public function deleteAction()
  {
    // DELETE functionality is achieved through PUT with a null translation
    throw new Api_Exception_BadRequest();
  }

  protected function _getEntryIndexForLocale($existing, $locale)
  {
    foreach ($existing as $index => $entry) {
      if ($entry['locale'] === $locale) {
        return $index;
      }
    }
    return false;
  }
}