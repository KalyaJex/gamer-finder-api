<?php

namespace App\Services;

use App\Models\Game;
use App\Models\UserGame;
use App\Repositories\UserRepository;
use App\Repositories\GameRepository;
use App\Repositories\UserGameRepository;

class UserProfileService {

  public function __construct(
    private UserRepository $userRepository,
    private GameRepository $gameRepository,
    private UserGameRepository $userGameRepository,
    private RedisService $redisService) {}
  
  public function getUserProfile(int $userId) {
    $user = $this->userRepository->findById($userId);

    if (!empty($user)) {
      $userGamePairs = $this->userGameRepository->findByUserId($userId);

      $gameIds = [];
      foreach ($userGamePairs as $userGame) {
        $gameIds[] = $userGame->game_id;
      }
      $user->gameIds = $gameIds;

      return [
        'status' => 200,
        'body' => $user,
      ];
    } else {
      return [
        'status' => 404,
        'body' => [
          'error' => 'UserNotFound',
          'message' => 'User not found',
        ]
      ];
    }
  }

  public function getAllUsers() {
    $users = $this->userRepository->findAll();

    if (!empty($users)) {
      return [
        'status' => 200,
        'body' => $users,
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
  }

  public function updateUserProfile(int $userId, array $data) {
    $user = $this->userRepository->findById($userId);
    if (empty($user)) {
      return [
        'status' => 404,
        'body' => [
          'error' => 'UserNotFound',
          'message' => 'User not found',
        ]
      ];
    }

    $user->username = $data['username'] ?? $user->username;
    $user->email = $data['email'] ?? $user->email;

    if ($this->userRepository->update($user)) {
      return [
        'status' => 200,
        'body' => [
          'message' => 'Profile updated succesfully',
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
  }
  
  public function addGameToProfile(int $steamAppId,  string $title, string $genre, string $releaseDate, int $userId) {

    $game = $this->gameRepository->fetchBySteamAppId($steamAppId);

    if (empty($game)) {
      $game = new Game($steamAppId, $title, $genre, $releaseDate);
      if ($this->gameRepository->create($game)) {
        $game = $this->gameRepository->fetchBySteamAppId($game->steam_app_id);
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

    $userGame = $this->userGameRepository->findByGameIdUserId($game);

    if (empty($userGame)) {
      $userGame = new UserGame($userId, $game->id);
      if ($this->userGameRepository->create($userGame)) {
        $this->redisService->unlink("user:$userId:similarities");
        return [
          'status' => 200,
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
        'status' => 409, 
        'body' => [
          'error' => 'UserGameAlreadyExist',
          'message' => 'The user game pair already exist'
        ]
      ];
    }
  }

  public function removeGameFromProfile(int $userId, int $gameId) {
    $userGame = $this->userGameRepository->findByGameIdUserId(new UserGame($userId, $gameId));

    if (!empty($userGame)) {
      if ($this->userGameRepository->delete($userId, $gameId)) {
        return [
          'status' => 200,
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
        'status' => 400, 
        'body' => [
          'error' => 'UserGamedoesntExist',
          'message' => 'The user game pair doesnt exist'
        ]
      ];
    }
  }

  public function getAllGamesFromProfile(int $userId) {
    $userGames = $this->userGameRepository->findByUserId($userId) ?? [];
    return [
      'status' => 200,
      'body' => [
        'userGames' => $userGames
      ]
    ];
  }

  public function getUserDetails(int $userId) {
    $user = $this->userRepository->findById($userId);
    $userGamePairs = $this->userGameRepository->findByUserId($userId);
  }
}