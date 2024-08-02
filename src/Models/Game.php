<?php

namespace App\Models;

class Game extends GameModel {
  public function __construct(int $steam_app_id, string $title, string $genre, string $release_date)
  {
    $this->steam_app_id = $steam_app_id;
    $this->title = $title;
    $this->genre = $genre;
    $this->release_date = $release_date;
  }
}
