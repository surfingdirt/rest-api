<?php
class Api_Checkin_DurationValidator extends Zend_Validate_Abstract
{
    const INVALID = 'intInvalid';
    const NOT_INT = 'notInt';
    const TOO_BIG = 'tooBig';
    
    const MAX = 28400; // 8 * 3600
    
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid duration given, should be a number lower than 8 * 3600",
        self::NOT_INT => "'%value%' does not appear to be a number",
        self::TOO_BIG => "Invalid duration given, should be lower than 8 * 3600"
	);

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid integer
     *
     * @param  string|integer $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);

        if (strval(intval($value)) != $value) {
            $this->_error(self::NOT_INT);
            return false;
        }

        if (strval(intval($value)) > self::MAX) {
            $this->_error(self::TOO_BIG);
            return false;
        }

        return true;
	}
}