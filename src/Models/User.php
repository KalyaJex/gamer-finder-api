<?php

namespace App\Models;

class User extends UserModel {
  public function __construct($data = [])
  {
    var_dump($data);
    if (!empty($data['username'])) {
      $this->username = sanitizeInput($data['username']);
    }
    if (!empty($data['email'])) {
      $this->email = sanitizeInput($data['email']);
    }
    $this->password = $data['password'];
    $this->is_email_confirmed = false;
    $this->email_confirmation_token = bin2hex(random_bytes(16));
  }
}