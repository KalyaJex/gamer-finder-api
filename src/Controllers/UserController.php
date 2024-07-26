<?php

namespace App\Controllers;

require __DIR__ . '/../../vendor/autoload.php';

use App\Models\UserModel;
use App\Repositories\UserRepository;

class UserController {

  public function __construct(private UserRepository $userRepository, private AuthController $authController) {}

  public function register($data) {
    $data['username'] = sanitizeInput($data['username'] );
    $data['email'] = sanitizeEmail($data['email'] );
    $user = new UserModel($data);

    $errors = $user->validateUser();
    if (!empty($errors)) {
      return ['status' => 400, 'body' => ['errors' => $errors]];
    }

    if ($this->userRepository->getUsernameExists($user->username)) {
      return ['status' => 409, 'body' => ['error' => 'Username already taken']];
    }
    if ($this->userRepository->getEmailExists($user->email)) {
      return ['status' => 409, 'body' => ['error' => 'Email address already used']];
    }

    $user->password = password_hash($user->password, PASSWORD_DEFAULT);
    $result = $this->userRepository->create($user);
    $userId = $result['userId'];
    $success = $result['success'];

    if ($success) {
      $accessToken = $this->authController->generateAccessToken($result['userId']);
      $refreshToken  = $this->authController->generateRefreshToken($result['userId']);
      return [
        'status' => 201, 
        'body' => [
          'message' => 'User registered successfully',
          'access_token' => $accessToken,
          'refresh_token' => $refreshToken,
        ],
      ];
    } else {
      return ['status' => 500, 'body' => ['error' => 'Internal Server Error']];
    }
  }

  public function login($data) {
    $data['password'] = sanitizeInput($data['password'] );
    $data['email'] = sanitizeEmail($data['email'] );

    $user = $this->userRepository->fetchByEmail($data['email']);

    if(empty($user) && !password_verify($data['password'], $user['password'])) {
      return ['status' => 401, 'body' => ['error' => 'Invalid credentials']];
    }

    $accessToken = generateAccessToken($user->id);
    $refreshToken = generateRefreshToken($user->id);

  }
}