<?php
class Lib_Validate_DateTime_Simple extends Zend_Validate_Abstract
{
    const NOT_VALID = 'notValid';
    const DATE_PAST = 'dateTooFarInThePast';

    /**
     * Number of seconds in the past that a date is allowed
     * @var int
     */
    const ALLOWED_PAST_OFFSET = 20;
    
	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => "dateTimeNotValid",
    );

    protected $_pastAllowed = true;
    
    public function setPastAllowed($bool)
    {
    	$this->_pastAllowed = $bool;
    }
    
    public function isValid($value)
    {
		$preg = '/^\d{4}(\-)\d{2}(\-)\d{2}( \d{2}:\d{2}:\d{2})?$/';

		if (!preg_match($preg, $value)) {
            $this->_error(self::NOT_VALID);
            return false;
        }
        
        if(!$this->_pastAllowed) {
	        $now = Utils::date('timestamp');
	        $date = strtotime($value);
	        
	        if($date < $now - self::ALLOWED_PAST_OFFSET) {
	            $this->_error(self::DATE_PAST);
	            return false;
	        }
        }        
        return true;
    }
}