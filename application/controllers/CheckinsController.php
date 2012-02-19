<?php
class CheckinsController extends Api_Controller_Action
{
	/**
	 * 
	 * The amount of seconds before or after 'now' that a checkin
	 * becomes or stays active.
	 * @var integer
	 */
	const CHECKIN_MARGIN = 1800;
	
	protected function _getAllObjects($where, $sort = null, $dir = null, $count = null, $start = null)
	{
		/*
		 * TODO: refactor this mess.
		 * Move code to the Api_Checkin_Accessor 
		 */
		
    	$db = $this->_table->getAdapter();
    	$checkinsTable = Constants_TableNames::CHECKIN;
    	$locationsTable = Constants_TableNames::LOCATION;
    	$checkinType = Spot::ITEM_TYPE;
    	$margin = self::CHECKIN_MARGIN;
    	
    	$spotId = $this->_request->getParam('spotId');
    	$riderId = $this->_request->getParam('riderId');
    	$countryId = $this->_request->getParam('countryId');
    	$regionId = $this->_request->getParam('regionId');
    	$date = $this->_request->getParam('date', Utils::date("Y-m-d H:i:s"));
    	$onlyCurrent = $this->_request->getParam('onlyCurrent', false);
    	$fetchAround = $this->_request->getParam('fetchAround', false);
    	
    	if($spotId === '0' || $riderId === '0' || $countryId === '0' || $regionId === '0') {
    		throw new Api_Exception_BadRequest();
    	}

    	$order = "c.id ASC";
    	$selectDistance = "";
    	if($fetchAround) {
    		if($spotId) {
    			try {
    				$spot = Data::factory($spotId, Spot::ITEM_TYPE, true);
    			} catch (Lib_Exception_NotFound $e) {
    				throw new Api_Exception_NotFound();
    			}
    			$location = $spot->getLocation();
    			if(!$location) {
    				throw new Api_Exception("Spot '$spotId' has no location");
    			}
    			$lat = $location->latitude;
    			$lon = $location->longitude;
    			
    		} else {
		    	$lat = $this->_request->getParam('lat');
		    	$lon = $this->_request->getParam('lon');
    		}
    		if(!is_numeric($lat) || $lat > 90 || $lat < -90) {
    			throw new Api_Exception_BadRequest(1);
    		}
    			
    		if(!is_numeric($lon) || $lon > 180 || $lon < -180) {
    			throw new Api_Exception_BadRequest(2);
    		}
    		$distance = "(l.latitude - $lat) * (l.latitude - $lat) + (l.longitude - $lon) * (l.longitude - $lon)";
    		$selectDistance = ",\n".$distance. " AS distance2 ";
    		$order = "$distance ASC";
    		
    	} elseif($spotId) {
    		$where .= $db->quoteInto(' AND spot = ?', $spotId);
    	} elseif($riderId) {
    		$where .= $db->quoteInto(' AND c.submitter = ?', $riderId);
    	} elseif($countryId) {
    		$where .= $db->quoteInto(' AND l.country = ?', $countryId);
    	} elseif($regionId) {
    		$where .= $db->quoteInto(' AND l.dpt = ?', $regionId);
    	}

   		$validator = new Lib_Validate_DateTime_Simple();
   		if(!$validator->isValid($date, true)) {
   			throw new Api_Exception_BadRequest();
    	}
    	$where .= " AND '$date' BETWEEN DATE_SUB(c.checkinDate, INTERVAL checkinDuration SECOND) AND DATE_ADD(DATE_ADD(c.checkinDate, INTERVAL checkinDuration SECOND), INTERVAL $margin SECOND)";

    	$sql = <<<SQL
SELECT c.id, c.checkinDuration,
c.checkinDate AS checkinStart,
DATE_ADD(c.checkinDate, INTERVAL checkinDuration SECOND) AS checkinEnd,
DATE_SUB(c.checkinDate, INTERVAL $margin SECOND) AS checkinReportStart,
DATE_ADD(DATE_ADD(c.date, INTERVAL checkinDuration SECOND), INTERVAL $margin SECOND) AS checkinReportEnd$selectDistance
FROM $checkinsTable c
LEFT JOIN $locationsTable l
ON l.itemId = c.spot AND l.itemType = '$checkinType'
WHERE $where
ORDER BY $order
LIMIT $count
SQL;
    	//error_log(strtr($sql, "\n\t", '  '));
    	$results = $db->fetchAll($sql);
    	
    	$checkins = array();
    	$table = new Api_Checkin();
    	foreach($results as $res) {
    		if(!isset($res['id'])) {
    			continue;
    		}
    		
    		// Take advantage of built-in cache in our table models:
    		$result = $table->find($res['id']);
    		if($checkin = $result->current()) {
    			$checkins[] = $checkin;
    		}
    	}
    	
		return $checkins;
	}	
	
    protected function _getWhereClause(User_Row $user)
    {
    	$db = $this->_table->getAdapter();
		$return = $db->quoteInto('(c.status = ?', Data::VALID);
		$return .= $db->quoteInto(' OR c.submitter = ?)', $user->getId());
		return $return;
    }
}