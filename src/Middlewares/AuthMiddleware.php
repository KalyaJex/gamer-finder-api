<?php

namespace App\Middleware;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
  private static $key = "qqqq";
  private static $encrypt = ['HS256'];

  public static function generateToken($data) {
    $time = time();
    $token = [
      'iat' => $time,
      'exp' => $time + 3600,
      'data' => $data,
    ];
    return JWT::encode($token, self::$key, self::$encrypt[0]);
  }

  public static function validateToken($token) {
    try {
      $decoded = JWT::decode($token, new Key(self::$key, self::$encrypt[0]));
      return $decoded->data;
    } catch (Exception $e) {
      return null;
    }
  }

  public static function getBearerToken() {
    $headers = null;
    if(isset($_SERVER['Authorization'])) {
      $headers = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
      $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
      $requestHeaders = apache_request_headers();
      if (isset($requestHeaders['Authorization'])) {
        $headers = trim($requestHeaders['Authorization']);
      }
    }

    if(!empty($headers)) {
      if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
      }
    }
    return null;
  }
}