<?php

function validatePassword(string $password, int $length, int $maxLength): bool {
  $containsLetter = '/[a-zA-Z]/';
  $containsNumber = '/[0-9]/';
  $containsSpecialChar = '/[!@#\$%\^&\*\(\)\-_\=\+\{\}\[\]\|\\:;\"\'<>,\.\?\/~`]/';
  return strlen($password) < $length && strlen($password) >= $maxLength && preg_match($containsLetter, $password) && preg_match($containsNumber, $password) && preg_match($containsSpecialChar, $password);
}

function validateUsername(string $username, int $maxLength): bool {
  $pattern = '/^[a-zA-Z0-9_]+$/';
  return strlen($username) >= $maxLength && preg_match($pattern, $username);
}