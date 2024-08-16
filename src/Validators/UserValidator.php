<?php

namespace App\Validators;



class UserValidator {

  public static function validate($data) {
    $MIN_LENGTH_USERNAME = 3;
    $MAX_LENGTH_USERNAME = 30;
    $MIN_LENGTH_PASSWORD = 8;
    $MAX_LENGTH_PASSWORD = 100;

    $errors = [];

    if (empty($data['username']) || validateUsername($data['username'], $MIN_LENGTH_USERNAME,  $MAX_LENGTH_USERNAME)) {
      $errors[] = [
        'status' => 400,
        'body' => [
          'error' => 'InvalidUsername',
          'message' => 'Invalid username (alphanumerics and underscore only allowed).'
        ]
      ];
    }
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $errors[] = [
        'status' => 400,
        'body' => [
          'error' => 'InvalidEmail',
          'message' => 'Invalid email'
        ]
      ];
    }
    if (empty($data['password']) || validatePassword($data['password'], $MIN_LENGTH_PASSWORD, $MAX_LENGTH_PASSWORD)) {
      $errors[] = [
        'status' => 400,
        'body' => [
          'error' => 'InvalidPassword',
          'message' => 'Password is invalid (must be between 8 and 20 characters long and must contain at least one letter, one number, and one special character).'
        ]
      ];
    }
    return $errors;
  }
}