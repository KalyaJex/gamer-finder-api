<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/inc/all.inc.php';
require __DIR__ . '/src/Helpers/index.php';


date_default_timezone_set('Europe/Zurich');

$container = new \App\Core\Container();


$container->bind('pdo', function() {
  return (new \App\Core\Database())->getConnection();
});

$container->bind('database', function() {
  return new \App\Core\Database();
});

$container->bind('router', function() {
  return new \App\Core\Router();
});

$container->bind('userRepository', function() use($container) {
  $db = $container->get('database');
  return new \App\Repositories\UserRepository($db);
});

$container->bind('tokenRepository', function() use($container) {
  $db = $container->get('database');
  return new \App\Repositories\TokenRepository($db);
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
  return new \App\Services\UserProfileService($userRepository);
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