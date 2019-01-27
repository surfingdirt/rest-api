<?php

class LocationsController extends Api_Controller_Action
{
  public function listAction()
  {
    $accessors = $resources = array();

    $swLat = $this->_request->getParam('swLat');
    $swLon = $this->_request->getParam('swLon');
    $neLat = $this->_request->getParam('neLat');
    $neLon = $this->_request->getParam('neLon');

    $countryId = $this->_request->getParam('countryId');
    $regionId = $this->_request->getParam('regionId');

    $lat = $this->_request->getParam('lat');
    $lon = $this->_request->getParam('lon');
    $threshold = $this->_request->getParam('max', LOCATION_DIST_MAX_KM);

    if ($swLat && $swLon && $neLat && $neLon) {
      // Request within bounds
      $objects = $this->_getObjectsWithinBounds(array($swLat, $swLon, $neLat, $neLon));
    } elseif ($countryId || $regionId) {
      // Request within an area
      $objects = $this->_getObjectsInArea($countryId, $regionId);
    } elseif ($lat && $lon) {
      // Request around a point
      $objects = $this->_getObjectsAround($lat, $lon);
    } else {
      // Not a request for 'within bounds', nor within an area
      throw new Api_Exception_BadRequest();
    }

    foreach ($objects as $object) {
      $itemType = $object->getItemType();
      if (!isset($accessors[$itemType])) {
        list($dummy, $accessor) = $this->_mapResource($itemType);
        $accessors[$itemType] = $accessor;
      } else {
        $accessor = $accessors[$itemType];
      }

      if (!$object->isReadableBy($this->_user, $this->_acl)) {
        continue;
      }

      $resource = $accessor->getObjectData($object);
      $resource['itemType'] = $itemType;
      if ($lat && $lon) {
        $distance = Item::distance($lat, $lon, $resource['latitude'], $resource['longitude']);
        if ($distance > $threshold) {
          continue;
        }
        $resource['distance'] = round($distance, 2);
      }
      $resources[] = $resource;
    }

    $this->view->resources = $resources;
  }

  protected function _getObjectsWithinBounds($bounds)
  {
    foreach (array('swLat', 'swLon', 'neLat', 'neLon') as $index => $paramName) {
      $param = $this->_request->getParam($paramName);
      if (empty($param) || !is_numeric($param)) {
        throw new Api_Exception_BadRequest();
      }

      if ($index == 0 || $index == 2) {
        if ($param > 180 || $param < -180) {
          throw new Api_Exception_BadRequest();
        }
      }

      if ($index == 2 || $index == 3) {
        if ($param > 90 || $param < -90) {
          throw new Api_Exception_BadRequest();
        }
      }

      $bounds[] = $param;
    }

    $objects = Item::getItemsInBounds($bounds, null, true);
    return $objects;
  }

  protected function _getObjectsInArea($countryId, $regionId)
  {
    $return = Item::getItemsInArea($countryId, $regionId);
    return $return;
  }

  protected function _getObjectsAround($lat, $lon)
  {
    if (!is_numeric($lat) || !is_numeric($lon)) {
      throw new Api_Exception_BadRequest();
    }
    $return = Item::getItemsAround($lat, $lon);
    return $return;
  }

  public function getAction()
  {
    throw new Api_Exception_BadRequest();
  }

  public function putAction()
  {
    throw new Api_Exception_BadRequest();
  }

  public function postAction()
  {
    throw new Api_Exception_BadRequest();
  }

  public function deleteAction()
  {
    throw new Api_Exception_BadRequest();
  }
}