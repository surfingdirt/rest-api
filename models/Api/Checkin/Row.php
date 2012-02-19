<?php
class Api_Checkin_Row extends Data_Row
{
	protected $_formClass = 'Api_Checkin_Form';

	public function getFolderPath()
	{
		return null;
	}

    public function getSpot()
    {
        if(empty($this->spot)){
        	return null;
        }
    	$this->spot = $this->findParentRow('Spot');
        return $this->spot;
    }
	
	public function getCountry()
	{
		$spot = $this->getSpot();
		if(is_null($spot)) {
			return null;
		}		
		
		$country = $spot->getCountry();
		return $country;
	}

	public function getRegion()
	{
		$spot = $this->getSpot();
		if(is_null($spot)) {
			return null;
		}		
		
		$region = $spot->getDpt();
		return $region;
	}

	public function getLocation()
	{
		$spot = $this->getSpot();
		if(is_null($spot)) {
			return null;
		}		
		
		$location = $spot->getLocation();
		return $location;
	}
}