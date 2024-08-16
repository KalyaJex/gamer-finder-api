<?php

namespace App\Core;

class RedisKey {
  const USER_PROFILE = 'user:profile:%s';
  const USER_SIMILARITIES = 'user:%s:similarities';
}