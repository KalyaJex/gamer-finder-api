<?php

namespace App\Repositories;

require __DIR__ . '/../../vendor/autoload.php';

use App\Models\UserModel;
use App\Core\Database;
use PDO;

class UserRepository {

  public function __construct(private Database $db) {}

  public function create(UserModel $user) {
    $sql = 'INSERT INTO `users` (`username`, `email`, `password`) VALUES (:username, :email, :password)';
    $success = $this->db->query($sql, ['username' => $user->username, 'email' =>$user->email, 'password' => $user->password]);
    return ['success' => $success, 'userId' => $this->db->getconnection()->lastInsertId()];
  }

  public function delete(int $id) {
    $sql = 'DELETE FROM `users` where `id` =:id';
    $this->db->query($sql, ['id' => $id]);
  }

  public function fetchByUsername(string $username) {
    $sql = 'SELECT * FROM `users` WHERE `username` =:username';
    return $this->db->fetch($sql, ['username' => $username], UserModel::class);
  }

  public function fetchByEmail(string $email) {
    $sql = 'SELECT * FROM `users` WHERE `email` =:email';
    return $this->db->fetch($sql, ['email' => $email], UserModel::class);
  }

  public function fetchById(int $id) {
    $sql = 'SELECT * FROM `users` WHERE `id` =:id';
    return $this->db->fetch($sql, ['id' => $id], UserModel::class);
  }

  public function getUsernameExists(string $username): bool {
    $sql = 'SELECT COUNT(*) AS `count` FROM `users` WHERE username = :username';
    $stmt = $this->db->query($sql, ['username' => $username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result['count'] >= 1);
  }

  public function getEmailExists(string $email): bool {
    $sql = 'SELECT COUNT(*) AS `count` FROM `users` WHERE email = :email';
    $stmt = $this->db->query($sql, ['email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result['count'] >= 1);
  }
}