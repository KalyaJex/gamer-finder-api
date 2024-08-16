<?php

// header('Access-Control-Allow-Origin: http://localhost:4200');
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (isset($_SERVER['HTTP_ORIGIN'])) {
  // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or use a whitelist approach.
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

  exit(0);
}
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$redis->setOption(Redis::OPT_READ_TIMEOUT, -1);

require __DIR__ . '/vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/inc/all.inc.php';
require __DIR__ . '/src/Helpers/index.php';


date_default_timezone_set('Europe/Zurich');

/** \App\Core\Container @var $container */
$container = new \App\Core\Container();


$container->bind('pdo', function() {
  return (new \App\Core\Database())->getConnection();
});

$container->bind('database', function() {
  return new \App\Core\Database();
});

$container->bind('redisService', function() use($redis) {
  return new \App\Services\RedisService($redis);
});

$container->bind('router', function() {
  return new \App\Core\Router();
});

$container->bind('userRepository', function() use($container) {
  $db = $container->get('database');
  $redisService = $container->get('redisService');
  return new \App\Repositories\UserRepository($db, $redisService);
});

$container->bind('tokenRepository', function() use($container) {
  $db = $container->get('database');
  return new \App\Repositories\TokenRepository($db);
});

$container->bind('gameRepository', function() use($container) {
  $db = $container->get('database');
  $redisService = $container->get('redisService');
  return new \App\Repositories\GameRepository($db, $redisService);
});

$container->bind('userGameRepository', function() use($container) {
  $db = $container->get('database');
  $redisService = $container->get('redisService');
  return new \App\Repositories\UserGameRepository($db, $redisService);
});

$container->bind('emailService', function() {
  return new \App\Services\EmailService();
});

$container->bind('registrationService', function() use($container) {
  $userRepository = $container->get('userRepository');
  $emailService = $container->get('emailService');
  return new \App\Services\RegistrationService($userRepository, $emailService);
});

$container->bind('authenticationService', function() use($container) {
  $userRepository = $container->get('userRepository');
  $tokenRepository = $container->get('tokenRepository');
  return new \App\Services\AuthenticationService($userRepository, $tokenRepository);
});

$container->bind('userProfileService', function() use($container) {
  $userRepository = $container->get('userRepository');
  $gameRepository = $container->get('gameRepository');
  $userGameRepository = $container->get('userGameRepository');
  $redisService = $container->get('redisService');
  return new \App\Services\UserProfileService($userRepository, $gameRepository, $userGameRepository, $redisService);
});

$container->bind('sanitizer', function() {
  return new \App\Helpers\Sanitizer();
});

$container->bind('userController', function() use($container) {
  $userRepository = $container->get('userRepository');
  $registrationService = $container->get('registrationService');
  $authenticationService = $container->get('authenticationService');
  $userProfileService = $container->get('userProfileService');
  $userProfileService = $container->get('userProfileService');
  $sanitizer = $container->get('sanitizer');
  return new \App\Controllers\UserController($userRepository, $registrationService, $authenticationService, $userProfileService, $sanitizer);
});

$container->bind('authController', function() use($container) {
  $userRepository = $container->get('userRepository');
  return new \App\Controllers\AuthController($userRepository);
});




require __DIR__ . '/src/Routes/routes.php';