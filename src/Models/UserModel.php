<?php

namespace App\Models;

class UserModel {
  public int $id;
  public string $username;
  public string $email;
  public string $password;
  public int $creationDate;
  public bool $is_email_confirmed;
  public string $email_confirmation_token;
}