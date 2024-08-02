<?php

namespace App\Helpers;

class Sanitizer {
  

  public static function sanitize(array $data): array {
    $exception = [
      'password',
      'email',
    ];

    foreach ($data as $key => $value) {
      if ($key === 'email') {
        $data[$key] = filter_var($value, FILTER_SANITIZE_EMAIL);
      }
      if (in_array($key, $exception)) {
        continue;
      }
      $data[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    return $data;
  }
}