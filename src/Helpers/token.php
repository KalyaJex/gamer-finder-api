<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateAccessToken($userId) {
  $expiresAt = time() + $_ENV['AUTH_TOKEN_EXP'];
  $payload = [
    'user_id' => $userId,
    'exp' => $expiresAt,
  ];
  $key = $_ENV['ACCESS_SECRET_KEY'];
  return [JWT::encode($payload, $key, 'HS256'), $expiresAt];
}

function generateRefreshToken($userId) {
  $payload = [
    'user_id' => $userId,
    'exp' => time() + (30 * 24 * 60 * 60) // Token expires in 30 days
  ];
  $key = $_ENV['REFRESH_KEY'];
  return JWT::encode($payload, $key, 'HS256');
}

function validateTokenExpTime($createdTime) {
  $createdTime = strtotime($createdTime);
  $now = time();
  return $createdTime + $_ENV['REFRESH_TOKEN_EXP'] >= $now;
}

function validateToken($token, $key) {
  try {
      $decoded = JWT::decode($token, new Key($key, 'HS256'));
      return (array) $decoded;
  } catch (Exception $e) {
      return null;
  }
}