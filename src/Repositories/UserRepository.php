<?php

namespace App\Repositories;

require __DIR__ . '/../../vendor/autoload.php';

use App\Models\UserModel;
use App\Core\Database;
use PDO;

class UserRepository {

  public function __construct(private Database $db) {}

  public function create(UserModel $user) {
    $sql = 'INSERT INTO `users` (`username`, `email`, `password`, `is_email_confirmed`, `email_confirmation_token`) VALUES (:username, :email, :password, :is_email_confirmed, :email_confirmation_token)';
    return $this->db->query($sql, [
      'username' => $user->username, 
      'email' =>$user->email, 
      'password' => $user->password, 
      'is_email_confirmed' => $user->is_email_confirmed, 
      'email_confirmation_token' => $user->email_confirmation_token,
    ]);
  }

  public function update(UserModel $user) {
    $sql = 'UPDATE `users` SET `username` = :username, `email` = :email WHERE id = :id';
    return $this->db->query($sql, ['username' => $user->username, 'email' =>$user->email, 'id' => $user->id]);
  }

  public function delete(int $id) {
    $sql = 'DELETE FROM `users` where `id` =:id';
    $this->db->query($sql, ['id' => $id]);
  }

  public function findByUsername(string $username) {
    $sql = 'SELECT * FROM `users` WHERE `username` =:username';
    return $this->db->find($sql, ['username' => $username], UserModel::class);
  }

  public function findByEmail(string $email) {
    $sql = 'SELECT * FROM `users` WHERE `email` =:email';
    return $this->db->find($sql, ['email' => $email], UserModel::class);
  }

  public function findById(int $id) {
    $sql = 'SELECT * FROM `users` WHERE `id` =:id';
    return $this->db->find($sql, ['id' => $id], UserModel::class);
  }

  public function findAll() {
    $sql = 'SELECT * FROM `users`';
    return $this->db->findAll($sql, [], UserModel::class);
  }

  public function getUsernameExists(string $username): bool {
    return $this->db->getExist('users', ['username' => $username]);
  }

  public function getEmailExists(string $email): bool {
    return $this->db->getExist('users', ['email' => $email]);
  }

  public function findByEmailConfirmationToken($token) {
    $sql = 'SELECT * FROM `users` WHERE `email_confirmation_token` =:email_confirmation_token';
    return $this->db->find($sql, ['email_confirmation_token' => $token], UserModel::class);
  }

  public function confirmEmail($userId) {
    $sql = 'UPDATE `users` SET `is_email_confirmed` = :is_email_confirmed WHERE id = :id';
    return $this->db->query($sql, ['is_email_confirmed' => true, 'id' => $userId]);
  }
}