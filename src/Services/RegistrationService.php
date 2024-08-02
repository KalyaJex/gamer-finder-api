<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;


class RegistrationService {

  public function __construct(
    private UserRepository $userRepository,
    private EmailService $emailService) {}

  public function userRegister($data) {
    $errors = UserValidator::validate($data);

    if (!empty($errors)) {
      return $errors[0];
    }

    if ($this->userRepository->getUsernameExists($data['username'])) {
      return [
        'status' => 409, 
        'body' => [
          'error' => 'UsernameTaken',
          'message' => 'Username already taken'
        ]
      ];
    }
    if ($this->userRepository->getEmailExists($data['email'])) {
      return [
        'status' => 409, 
        'body' => [
          'error' => 'EmailTaken',
          'message' => 'Email address already used'
        ]
      ];
    }

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    $user = new User($data);

    if ($this->userRepository->create($user)) {

      if ($this->emailService->sendConfirmationEmail($user)) {
        return [
          'status' => 201, 
          'body' => [
            'message' => 'User registered successfully',
  
          ],
        ];
      } else {
        return [
          'status' => 500, 
          'body' => [
            'error' => 'InternalServerError',
            'message' => 'Internal Server Error'
          ]
        ];
      }
      
    } else {
      return [
        'status' => 500, 
        'body' => [
          'error' => 'InternalServerError',
          'message' => 'Internal Server Error'
        ]
      ];
    }
  }
}