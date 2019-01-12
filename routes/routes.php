<?php
class Routes {
  static public function install(Zend_Controller_Front $frontController)
  {
    $router = $frontController->getRouter();
    Globals::setRouter($router);

    $dummyRoute = new Zend_Controller_Router_Route('/dummy/');

    $router->addRoutes(array(
      'default' => new Zend_Rest_Route($frontController),
      'test' => new Zend_Controller_Router_Route('test/:action', array('controller' => 'test', 'action' => 'freeze-timer')),

      'riderConfirmation' => new Zend_Controller_Router_Route('/riders/:id/confirmation/', array('controller' => 'custom', 'action' => 'rider-confirmation')),
      'lostPassword' => new Zend_Controller_Router_Route('/lost-password/', array('controller' => 'custom', 'action' => 'lost-password')),
      'activateNewPassword' => new Zend_Controller_Router_Route('/riders/:id/activate-new-password/', array('controller' => 'custom', 'action' => 'activate-new-password')),

      'userAlbums' => new Zend_Controller_Router_Route('/riders/:id/albums/', array('controller' => 'riders-albums', 'action' => 'list')),
      'comments' => new Zend_Controller_Router_Route('/:itemType/:itemId/comments/', array('controller' => 'comments')),
      'countryRegions' => new Zend_Controller_Router_Route('/countries/:countryId/regions/', array('controller' => 'regions', 'action' => 'list')),

      'countryLocations' => new Zend_Controller_Router_Route('/countries/:countryId/locations/', array('controller' => 'locations', 'action' => 'list')),
      'regionLocations' => new Zend_Controller_Router_Route('/regions/:regionId/locations/', array('controller' => 'locations', 'action' => 'list')),

      'checkinsBySpot' => new Zend_Controller_Router_Route('/checkins/spots/:spotId/', array('controller' => 'checkins', 'action' => 'list')),
      'checkinsBySpotCurrent' => new Zend_Controller_Router_Route('/checkins/spot/:spotId/current/', array('controller' => 'checkins', 'action' => 'list', 'onlyCurrent' => true)),
      'checkinsByCountry' => new Zend_Controller_Router_Route('/checkins/countries/:countryId/', array('controller' => 'checkins', 'action' => 'list')),
      'checkinsByRegion' => new Zend_Controller_Router_Route('/checkins/regions/:regionId/', array('controller' => 'checkins', 'action' => 'list')),
      'checkinsByRider' => new Zend_Controller_Router_Route('/checkins/riders/:riderId/', array('controller' => 'checkins', 'action' => 'list')),
      'checkinsByRiderCurrent' => new Zend_Controller_Router_Route('/checkins/riders/:riderId/current/', array('controller' => 'checkins', 'action' => 'list', 'onlyCurrent' => true)),
      'checkinsAroundSpot' => new Zend_Controller_Router_Route('/checkins/spots/:spotId/around/', array('controller' => 'checkins', 'action' => 'list', 'fetchAround' => true)),
      'checkinsAroundLocation' => new Zend_Controller_Router_Route('/checkins/around/', array('controller' => 'checkins', 'action' => 'list', 'fetchAround' => true)),

      'createdata' => $dummyRoute,
      'editdata' => $dummyRoute,
      'deletedata' => $dummyRoute,
      'userregister' => $dummyRoute,
      'uploadvideo' => $dummyRoute,
      'postcomment' => $dummyRoute,
      'userupdate' => $dummyRoute,
      'createalbum' => $dummyRoute,
      'editalbum' => $dummyRoute,
      'editcomment' => $dummyRoute,
      'uploadphotomain' => $dummyRoute,
      'editvideo' => $dummyRoute,
    ));
  }
}