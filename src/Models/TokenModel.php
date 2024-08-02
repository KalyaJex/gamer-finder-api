<?php

namespace App\Models;

class TokenModel {
  public int $id;
  public int $user_id;
  public string $token;
  public string $user_agent;
  public string $created;
}