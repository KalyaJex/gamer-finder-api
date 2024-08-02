<?php

namespace App\Repositories;

use App\Models\UserModel;
use App\Core\Database;
use App\Models\TokenModel;
use PDO;

class TokenRepository {

  public function __construct(private Database $db) {}

  public function create(TokenModel $token): bool {
    $sql = 'INSERT INTO `refresh_tokens` (`user_id`, `token`, `user_agent`) VALUES (:userId, :token, :userAgent)';
    return $this->db->query($sql, ['userId' => $token->user_id, 'token' =>$token->token, 'userAgent' => $token->user_agent]);
  }

  public function findByToken(string $token) {
    $sql = 'SELECT * FROM `refresh_tokens` WHERE `token` =:token';
    return $this->db->find($sql, ['token' => $token], TokenModel::class);
  }

  public function findAllByUserId(int $userId) {
    $sql = 'SELECT * FROM `refresh_tokens` WHERE `user_id` =:user_id';
    return $this->db->findAll($sql, ['user_id' => $userId], TokenModel::class);
  }

  public function delete(string $token) {
    $sql = 'DELETE FROM `refresh_tokens` WHERE `token` =:token';
    return $this->db->query($sql, ['token' => $token]);
  }

  public function tokenExist(string $token, $user_id): bool {
    return $this->db->getExist('refresh_tokens', ['token' => $token, 'user_id' => $user_id]);
  }
}