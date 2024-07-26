<?php

namespace App\Models;

class UserModel {
  public int $id;
  public string $username;
  public string $email;
  public string $password;

  private int $maxLengthUsername = 20;
  private int $maxLengthPassword = 20;
  private int $minLengthPassword = 8;

  public function __construct($data = [])
  {
    if (!empty($data['username'])) {
      $this->username = sanitizeInput($data['username']);
    }
    if (!empty($data['email'])) {
      $this->email = sanitizeInput($data['email']);
    }
    if (!empty($data['password'])) {
      $this->password = sanitizeInput($data['password']);
    }
  }


  public function validateUser() {
    $errors = [];
    if (empty($this->username) || validateUsername($this->username, $this->maxLengthUsername)) {
      $error[] = 'Invalid username (alphanumerics and underscore only allowed).';
    }
    if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      $error[] = 'Invalid email';
    }
    if (empty($this->password) || validatePassword($this->password, $this->minLengthPassword, $this->maxLengthPassword)) {
      $error[] = 'Password is invalid (must be between 8 and 20 characters long and must contain at least one letter, one number, and one special character).';
    }
    return $errors;
  }

  // public function getUsers($limit) {
  //   $sql = 'SELECT * FROM `users` ORDER BY `id` ASC LIMIT :limit';

  //   $params = [
  //     'limit' => $limit,
  //   ];

  //   return $this->select($sql, $params);
  // }

  // public function register($username, $password) {
  //   $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  //   $sql = 'INSERT INTO `users` (`username`, `password`) VALUES (:username, :password)';

  //   $params = [
  //     'username' => $username,
  //     'password' => $hashedPassword,
  //   ];

  //   return $this->select($sql, $params);
  // }
}