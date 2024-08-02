<?php

namespace App\Controllers;

require __DIR__ . '/../../vendor/autoload.php';

use App\Models\UserModel;
use App\Repositories\UserRepository;
use App\Services\AuthenticationService;
use App\Services\RegistrationService;
use App\Services\UserProfileService;
use App\Validators\UserValidator;
use App\Helpers\Sanitizer;

class UserController {

  public function __construct(
    private UserRepository $userRepository, 
    private RegistrationService $registrationService,
    private AuthenticationService $authenticationService,
    private UserProfileService $userProfileService,
    private Sanitizer $sanitizer) {}

  public function register($data) {
    $data = Sanitizer::sanitize($data);
    $response = $this->registrationService->userRegister($data);

    http_response_code($response['status']);
    echo json_encode($response['body']);
  }

  public function update(int $userId, array $data) {
    $data['email'] = sanitizeEmail($data['email'] );
    $data['username'] = sanitizeInput($data['username'] );

    return $this->userProfileService->updateUserProfile($userId, $data);
  }

  public function login($data) {
    $data = Sanitizer::sanitize($data);

    $response = $this->authenticationService->authenticateUser($data['email'], $data['password']);

    http_response_code($response['status']);
    echo json_encode($response['body']);
  }

  public function logout($data) {
    $data = Sanitizer::sanitize($data);

    $response = $this->authenticationService->deleteRefreshToken($data['userId'], $data['userAgent']);

    http_response_code($response['status']);
    echo json_encode($response['body']);
  }

  public function refreshToken($data) {
    $data = Sanitizer::sanitize($data);

    $response = $this->authenticationService->refreshToken($data['refresh-token'], $data['userId']);
    http_response_code($response['status']);
    echo json_encode($response['body']);
  }

  public function confirmEmail($data) {
    if (!empty($data['token'])) {
      $sanitizedToken = sanitizeInput($data['token']);
      $user = $this->userRepository->findByEmailConfirmationToken($sanitizedToken);
      if (!empty($user)) {
        $this->userRepository->confirmEmail($user->id);
        echo json_encode(['message' => 'Email confirmed successfully']);
        return;
      }
    }
    http_response_code(400);
    echo json_encode(['error' => 'Invalid token']);
  }

  public function addGameToProfile($data) {
    $data = Sanitizer::sanitize($data);

    $response = $this->userProfileService->addGameToProfile($data['steamAppId'], $data['steamAppId'], $data['genre'], $data['releaseDate'], $data['userId']);
    http_response_code($response['status']);
    echo json_encode($response['body']);
  }

  public function removeGameFromProfile($data) {
    $data = Sanitizer::sanitize($data);

    $response = $this->userProfileService->removeGameFromProfile($data['userId'], $data['gameId']);
    http_response_code($response['status']);
    echo json_encode($response['body']);
  }

  public function getAllGamesFromProfile($data) {
    $data = Sanitizer::sanitize($data);
    
  }
}