<?php

namespace App\Repositories;

require __DIR__ . 'vendor/autoload.php';

use App\Models\GameModel;
use App\Core\Database;
use PDO;

class GameRepository {

  public function __construct(private Database $db) {}

  public function create(int $steamAppId, string $title, string $genre, int $releaseDate) {
    $sql = 'INSERT INTO `games` (`steam_app_id`, `title`, `genre`, `release_date`) VALUES (:steamAppId, :title, :genre, :releaseDate)';
    $this->db->query($sql, ['steam_app_id' => $steamAppId, 'title' =>$title, 'genre' => $genre, 'release_date' => $releaseDate]);
  }

  public function delete(int $id) {
    $sql = 'DELETE FROM `games` where `id` =:id';
    $this->db->query($sql, ['id' => $id]);
  }

  public function fetchByTitle(string $title) {
    $sql = 'SELECT * FROM `games` WHERE `title` =:title';
    return $this->db->fetch($sql, ['title' => $title], GameModel::class);
  }

  public function fetchById(int $id) {
    $sql = 'SELECT * FROM `games` WHERE `id` =:id';
    return $this->db->fetch($sql, ['id' => $id], GameModel::class);
  }

  public function getTitleExists(string $title): bool {
    $sql = 'SELECT COUNT(*) AS `count` FROM `games` WHERE title = :title';
    $stmt = $this->db->query($sql, ['username' => $title]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result['count'] >= 1);
  }
}