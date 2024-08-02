<?php

namespace App\Controllers;

require __DIR__ . '/../../vendor/autoload.php';


use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Exception;
use Firebase\JWT\Key;

class AuthController {

  public function __construct(private UserRepository $userRepository) {}

  public function authenticateUser($email, $password) {


    $user = $this->userRepository->findByEmail($email);

    if(!empty($user) && password_verify($password, $user->password)) {
      $accessToken = $this->generateAccessToken($user->id);
      $refreshToken = $this->generateRefreshToken($user->id);

      return [
        'authenticated' => true, 
        'accessToken' => $accessToken, 
        'refreshToken' => $refreshToken,
      ];
    } else {
      return ['authenticated' => false];
    }

  }

  public function generateAccessToken($userId) {
    $payload = [
      'user_id' => $userId,
      'exp' => time() + 3600 // Token expires in 1 hour
    ];
    $key = $_ENV['ACCESS_SECRET_KEY'];
    return JWT::encode($payload, $key, 'HS256');
  }

  public function generateRefreshToken($userId) {
    $payload = [
      'user_id' => $userId,
      'exp' => time() + (30 * 24 * 60 * 60) // Token expires in 30 days
    ];
    $key = $_ENV['REFRESH_KEY'];
    return JWT::encode($payload, $key, 'HS256');
  }

  public function validateToken($token, $key) {
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}
}