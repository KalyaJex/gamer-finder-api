<?php

namespace App\Repositories;

require __DIR__ . 'vendor/autoload.php';

use App\Models\UserGameModel;
use App\Core\Database;
use Exception;
use PDO;

class UserGameRepository {

  public function __construct(private Database $db, private PDO $pdo) {}

  public function create(int $userId, string $gameId) {
    $sql = 'INSERT INTO `user_games` (`user_id`, `game_id`) VALUES (:userId, :gameId)';
    $this->db->query($sql, ['userId' => $userId, 'gameId' =>$gameId]);
  }

  public function delete(int $userId, string $gameId) {
    $sql = 'DELETE FROM `user_games` WHERE `user_id` =:userId AND `game_id` =:gameId';
    $this->db->query($sql, ['userId' => $userId, 'gameId' =>$gameId]);
  }

  public function fetchByUserId(int $userId) {
    $sql = 'SELECT * FROM `user_games` WHERE `user_id` =:userId';
    return $this->db->fetchAll($sql, ['userId' => $userId], UserGameModel::class);
  }

  public function fetchByGameId(int $gameId) {
    $sql = 'SELECT * FROM `user_games` WHERE `game_id` =:gameId';
    return $this->db->fetchAll($sql, ['gameId' => $gameId], UserGameModel::class);
  }
}