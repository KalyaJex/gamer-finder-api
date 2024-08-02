<?php

// function generateAccessToken($userId) {
//   $payload = [
//     'user_id' => $userId,
//     'exp' => time() + 3600 // Token expires in 1 hour
//   ];
//   $key = $_ENV['ACCESS_SECRET_KEY'];
//   return JWT::encode($payload, $key, 'HS256');
// }

// function generateRefreshToken($userId) {
//   $payload = [
//     'user_id' => $userId,
//     'exp' => time() + (30 * 24 * 60 * 60) // Token expires in 30 days
//   ];
//   $key = $_ENV['REFRESH_KEY'];
//   return JWT::encode($payload, $key, 'HS256');
// }