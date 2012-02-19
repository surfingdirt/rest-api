<?php
class Lib_Validate_Album_Id extends Lib_Validate_Album
{
    protected $_album;
	
	public function isValid($value, $context = array())
	{
		if(!parent::isValid($value)) {
			return false;
		}
		
		$user = Globals::getUser();
		
		if(!in_array($value, array(1, 2)) && !($user->isEditor() || $user->isAdmin()) && $this->_album->albumType == Media_Album::TYPE_SIMPLE) {
			// Only check for ownership for regular users on 'non-main' simple albums 
			if($this->_album->getSubmitter()->getId() != $user->getId()) {
				throw new Api_Exception_Unauthorised();
			}
		}
		
		if($this->_album->albumType == Media_Album::TYPE_AGGREGATE){
			// mark error of badType
			$this->_error(self::ALBUMTYPENOTALLOWED);
			return false;
		}
		
		return true;
	}
	
	protected function _findData($value, $returnValue = false)
    {
        try {
    		$result = $this->_album = Media_Album_Factory::buildAlbumById($value);
    		$returnValue = ($result->status == Data::VALID);
        } catch (Exception $e) {
            $logMessage  = "Type: ".get_class($e).PHP_EOL;
            $logMessage .= "Code: ".$e->getCode().PHP_EOL;
            $logMessage .= "Message: ".$e->getMessage().PHP_EOL.$e->getTraceAsString();

            Globals::getLogger()->error($logMessage, Zend_Log::ERR);
        }

        if($returnValue){
        	return $result;
        }

        return !empty($result);
    }
}