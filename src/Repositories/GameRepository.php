<?php

namespace App\Repositories;

require __DIR__ . 'vendor/autoload.php';

use App\Models\GameModel;
use App\Core\Database;
use PDO;

class GameRepository {

  public function __construct(private Database $db) {}

  public function create(GameModel $game) {
    $sql = 'INSERT INTO `games` (`steam_app_id`, `title`, `genre`, `release_date`) VALUES (:steam_app_id, :title, :genre, :release_date)';
    $this->db->query(
      $sql, 
      [
        'steam_app_id' => $game->steam_app_id, 
        'title' =>$game->title, 
        'genre' => $game->genre, 
        'release_date' => $game->release_date
      ]
    );
  }

  public function delete(int $id) {
    $sql = 'DELETE FROM `games` where `id` =:id';
    $this->db->query($sql, ['id' => $id]);
  }

  public function fetchByTitle(string $title) {
    $sql = 'SELECT * FROM `games` WHERE `title` =:title';
    return $this->db->find($sql, ['title' => $title], GameModel::class);
  }

  public function fetchBySteamAppId(string $steam_app_id) {
    $sql = 'SELECT * FROM `games` WHERE `steam_app_id` =:steam_app_id';
    return $this->db->find($sql, ['steam_app_id' => $steam_app_id], GameModel::class);
  }

  public function fetchById(int $id) {
    $sql = 'SELECT * FROM `games` WHERE `id` =:id';
    return $this->db->find($sql, ['id' => $id], GameModel::class);
  }

  public function getSteamAppIdExists(string $steam_app_id): bool {
    return $this->db->getExist('games', ['steam_app_id' => $steam_app_id]);
  }
}