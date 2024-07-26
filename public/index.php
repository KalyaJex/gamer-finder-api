<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/inc/all.inc.php';


date_default_timezone_set('Europe/Zurich');

$container = new \App\Support\Container();


$container->bind('pdo', function() {
  return (new \App\System\Database())->getConnection();
});

$container->bind('database', function() {
  return new \App\System\Database();
});

$container->bind('router', function() {
  return new \App\System\Router();
});

$container->bind('userRepository', function() use($container) {
  $db = $container->get('database');
  return new \App\Repository\UserRepository($db);
});

$container->bind('userController', function() use($container) {
  $userRepository = $container->get('userRepository');
  return new \App\Api\Controller\UserController($userRepository);
});

$container->bind('authController', function() use($container) {
  $userRepository = $container->get('userRepository');
  return new \App\Api\Controller\AuthController($userRepository);
});

require __DIR__ . '/routes.php';