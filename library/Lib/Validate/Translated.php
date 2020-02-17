<?php

class Lib_Validate_Translated extends Zend_Validate_Abstract
{
  const EMPTY_TEXT = 'emptyText';
  const MISSING_LOCALE = 'missingLocale';
  const MISSING_TEXT = 'missingText';
  const NOT_TEXT = 'notText';
  const UNSUPPORTED_LOCALE = 'unsupportedLocale';
  const WRONG_FORMAT = 'wrongFormat';

  public function __construct($textMustNotBeEmpty = false) {
    $this->_textMustNotBeEmpty = $textMustNotBeEmpty;
  }

  public function isValid($value)
  {
    if (!is_array($value)) {
      $this->_error(self::WRONG_FORMAT, json_encode($value));
      return false;
    }

    foreach ($value as $entry) {
      if (!isset($entry['locale'])) {
        $this->_error(self::MISSING_LOCALE, json_encode($entry));
        return false;
      }

      if (!isset($entry['text'])) {
        $this->_error(self::MISSING_TEXT, json_encode($entry));
        return false;
      }

      $supportedLocales = explode(',', SUPPORTED_LOCALES);
      if (!in_array($entry['locale'], $supportedLocales)) {
        $this->_error(self::UNSUPPORTED_LOCALE, json_encode($entry));
        return false;
      }

      if (!is_string($entry['text'])) {
        $this->_error(self::NOT_TEXT, json_encode($entry));
        return false;
      }

      if ($this->_textMustNotBeEmpty && !$entry['text']) {
        $this->_error(self::EMPTY_TEXT, json_encode($entry));
        return false;
      }

    }

    return true;
  }
}