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

      'userConfirmation' => new Zend_Controller_Router_Route('/user/:id/confirmation/', array('controller' => 'custom', 'action' => 'user-confirmation')),
      'userOAuth' => new Zend_Controller_Router_Route('/user/oauth/', array('controller' => 'user', 'action' => 'oauth-creation')),
      'lostPassword' => new Zend_Controller_Router_Route('/lost-password/', array('controller' => 'custom', 'action' => 'lost-password')),
      'activateNewPassword' => new Zend_Controller_Router_Route('/user/:id/activate-new-password/', array('controller' => 'custom', 'action' => 'activate-new-password')),

      'me' => new Zend_Controller_Router_Route('/user/me/', array('controller' => 'user', 'action' => 'me')),

      'emailExists' => new Zend_Controller_Router_Route('/user/email-exists/', array('controller' => 'user', 'action' => 'email-exists')),
      'usernameExists' => new Zend_Controller_Router_Route('/user/username-exists/', array('controller' => 'user', 'action' => 'username-exists')),

      'userAlbums' => new Zend_Controller_Router_Route('/user/:id/albums/', array('controller' => 'useralbum', 'action' => 'list')),

      'comments' => new Zend_Controller_Router_Route('/:itemType/:itemId/comments/', array('controller' => 'comments', 'action' => 'list')),

      'translations' => new Zend_Controller_Router_Route('/translations/:itemType/:itemId/', array('controller' => 'translation', 'action' => 'index' )),

      'displaydata' => $dummyRoute,
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