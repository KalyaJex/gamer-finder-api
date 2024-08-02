<?php

namespace App\Models;

use App\Models\TokenModel;

class Token extends TokenModel {
  public function __construct(int $userId)
  {
    $this->user_id = $userId;
    $this->token = bin2hex(random_bytes(16));
    $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $this->created = timestampUnixToSql(time());
  }
}