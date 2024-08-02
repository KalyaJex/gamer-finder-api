<?php 

$router = $container->get('router');
$BASE_URI = '/gamer-finder-api/';


// Routes :
// /auth/login
// /auth/refresh-token

// /user


// Login endpoint

$router->add('POST', $BASE_URI . 'auth/login', function() use($container) {

  $userController = $container->get('userController');

  $data = json_decode(file_get_contents('php://input'), true);

  $userController->login($data);

});

$router->add('POST', $BASE_URI . 'auth/logout', function() use($container) {

  $userController = $container->get('userController');

  $data = json_decode(file_get_contents('php://input'), true);

  $userController->logout($data);

});

// refresh token endpoint

$router->add('GET',  $BASE_URI . 'auth/refresh-token', function() use($container) {
  $authController = $container->get('authController');
  $userController = $container->get('userController');
  $data = $_GET['userId'];
  $data['refresh-token'] = $_COOKIE['refresh-token'];

  $userController->refreshToken($data);
});

$router->add('POST',  $BASE_URI . 'user', function() use($container) {
  $userController = $container->get('userController');

  $data = json_decode(file_get_contents('php://input'), true);

  $userController->register($data);
});

$router->add('GET', $BASE_URI . 'user/verification', function() use($container) {
  $userController = $container->get('userController');

  $userController->confirmEmail($_GET);
});

$router->add('POST', $BASE_URI . 'user/{userId}/game', function($userId) use($container) {
  $userController = $container->get('userController');
  $data = json_decode(file_get_contents('php://input'), true);
  $data['userId'] = (int)$userId;
  $userController->addGameToProfile($data);
});

$router->add('DELETE', $BASE_URI . 'user/{userId}/game/{gameId}', function($userId, $gameId) use($container) {
  $userController = $container->get('userController');
  $data = json_decode(file_get_contents('php://input'), true);
  $data['userId'] = (int)$userId;
  $data['gameId'] = (int)$gameId;
  $userController->removeGameFromProfile($data);
});

$router->add('POST', $BASE_URI . 'user/{userId}/games', function($userId) use($container) {
  $userController = $container->get('userController');
  $data = json_decode(file_get_contents('php://input'), true);
  $data['userId'] = (int)$userId;
  $userController->getAllGamesFromProfile($data);
});

$router->add('GET', $BASE_URI . 'test-database', function() use($container) {
  var_dump($_COOKIE);
  $db = $container->get('database');
  var_dump($db->getconnection());
});

$router->add('GET', $BASE_URI . 'test-dynamic-routing/{test}', function($test) {
  var_dump($test);
});


$method = $_SERVER['REQUEST_METHOD'];

$path =explode('?',$_SERVER['REQUEST_URI'])[0];

$router->dispatch($method, $path);
