<?php

namespace App\Services;

use App\Models\Game;
use App\Models\UserGame;
use App\Repositories\UserRepository;
use App\Repositories\GameRepository;
use App\Repositories\UserGameRepository;

class GameService {

  public function __construct(
    private UserRepository $userRepository,
    private GameRepository $gameRepository,
    private UserGameRepository $userGameRepository) {}

  public function
}