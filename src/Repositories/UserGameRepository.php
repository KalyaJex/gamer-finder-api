<?php

namespace App\Repositories;

require __DIR__ . '/../../vendor/autoload.php';

use App\Models\UserGameModel;
use App\Core\Database;
use App\Services\RedisService;
use Exception;
use PDO;

class UserGameRepository {

  public function __construct(
    private Database $db,
    private RedisService $redisService) {}

  public function create(UserGameModel $userGame) {
    $sql = 'INSERT INTO `user_games` (`user_id`, `game_id`) VALUES (:user_id, :game_id)';
    $this->db->query($sql, ['userId' => $userGame->user_id, 'game_id' =>$userGame->game_id]);
  }

  public function delete(int $userId, string $gameId) {
    $sql = 'DELETE FROM `user_games` WHERE `user_id` =:userId AND `game_id` =:gameId';
    $this->db->query($sql, ['userId' => $userId, 'gameId' =>$gameId]);
  }

  public function findByUserId(int $user_id) {
    $redisKey = "userGame:id:$user_id";
    $sql = 'SELECT * FROM `user_games` WHERE `user_id` =:user_id';
    return $this->db->findAll($sql, ['user_id' => $user_id], UserGameModel::class);
  }

  public function getGamesByUserId(int $userId) {
    $redisKey = "userGame:userId:$userId";

    $gameIds = array_map(function($gameId) {
      return (int) $gameId;
    },$this->redisService->redisQueryList($redisKey, [$this->db, 'getColumn'], 'user_games', 'game_id', ' WHERE `user_id` =:user_id', ['user_id' => $userId]));
    return $gameIds;
  }

  public function findByGameId(int $game_id) {
    $sql = 'SELECT * FROM `user_games` WHERE `game_id` =:game_id';
    return $this->db->findAll($sql, ['game_id' => $game_id], UserGameModel::class);
  }

  public function findByGameIdUserId(UserGameModel $userGame) {
    $sql = 'SELECT * FROM `user_games` WHERE `game_id` =:gameId AND `user_id` =:user_id' ;
    return $this->db->findAll($sql, ['userId' => $userGame->user_id, 'game_id' =>$userGame->game_id], UserGameModel::class);
  }
}