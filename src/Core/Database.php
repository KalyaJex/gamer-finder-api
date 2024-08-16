<?php

namespace App\Core;

use App\Services\RedisService;
use Exception;
use PDO;

class Database {
  private $pdo;
  private $host;
  private $dbName;
  private $username;
  private $password;


  public function __construct()
  {
    $this->host = $_ENV['DB_HOST'];
    $this->dbName = $_ENV['DB_DATABASE_NAME'];
    $this->username = $_ENV['DB_USERNAME'];
    $this->password = $_ENV['DB_PASSWORD'];
    $this->connect();
  }

  private function connect() {
    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
    
    $options = [
      \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES   => false,
    ];


    try {
      $this->pdo = new \PDO($dsn, $this->username, $this->password, $options);
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  public function getconnection() {
    return $this->pdo;
  }

  public function query(string $sql, array $params) {
    try {
      $stmt = $this->pdo->prepare($sql);
      $this->confirmQuery($sql, $stmt);

      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
      }

      return $stmt->execute();
    } catch (Exception $e) {
      $logger = new Logger($this);
      $logger->error($e->getMessage());
      throw new Exception("Could not perform query: $sql", 0, $e);
    }
  }

  public function getExist(string $tableName, array $params): bool {
    try {
      $searchString = '';
      foreach ($params as $key => $value) {
        $searchString .= "`$key` =:$key";
        if ($key !== array_key_last($params)) {
          $searchString .= ' AND ';
        }
      }
      $sql = 'SELECT COUNT(*) AS `count` FROM' . $tableName . 'WHERE ' . $searchString;
      $stmt = $this->pdo->prepare($sql);
      $this->confirmQuery($sql, $stmt);

      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
      }

      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      $result = $stmt->fetch();
      return ($result['count'] >= 1);
    } catch (Exception $e) {
      $logger = new Logger($this);
      $logger->error($e->getMessage());
      throw new Exception("Could not perform search on $tableName", 0, $e);
    }
  }

  public function find(string $sql, array $params, string $model) {
    $stmt = $this->pdo->prepare($sql);
    $this->confirmQuery($sql, $stmt);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }

    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, $model);
    $entry = $stmt->fetch();
    
    if (!empty($entry)) {
      return $entry;
    } else {
      return null;
    }
  }

  public function findAll(string $sql, array $params, string $model) {
    $stmt = $this->pdo->prepare($sql);
    $this->confirmQuery($sql, $stmt);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }

    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, $model);
    $result = $stmt->fetchAll();
    if (!empty($result)) {
      return $result;
    } else {
      return null;
    }
  }

  public function getColumn(string $table, string $column, string $extra = '', array $params = []) {
    $sql = "SELECT $column FROM $table" . $extra;
    $stmt = $this->pdo->prepare($sql);
    $this->confirmQuery($sql, $stmt);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($result)) {
      return $result;
    } else {
      return null;
    }
  }

  public function confirmQuery($sql, $stmt) {
    if ($stmt === false) {
      $logger = new Logger($this->pdo);
      $logger->error("Unable to do prepared statement: $sql");
      throw new Exception("Unable to do prepared statement: $sql" );
    }
  }

  public function log(string $text, string $level = 'debug', int $userId = null, int $authorizedBy = null) {
    $stmt = $this->pdo->prepare('INSERT INTO `log` (`user_id`, `level`, `text`) VALUES (:userId, :level, :text)');
    $stmt->bindValue('userId', $userId);
    $stmt->bindValue('level', $level);
    $stmt->bindValue('text', $text);
  }
}