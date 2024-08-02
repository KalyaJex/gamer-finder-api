<?php

namespace App\Models;

class UserGame extends UserGameModel {
  public function __construct(int $user_id, int $game_id)
  {
    $this->user_id = $user_id;
    $this->game_id = $game_id;
  }
}