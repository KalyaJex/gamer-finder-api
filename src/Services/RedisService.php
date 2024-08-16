<?php

namespace App\Services;

use Redis;

class RedisService {

  public function __construct(private Redis $redis) {}

  public function get($key) {
    return $this->redis->get($key);
  }

  public function set($key, $value) {
    return $this->redis->set($key, $value);
  }

  public function rPush($key, $value) {
    return $this->redis->rPush($key, $value);
  }

  public function getList($key) {
    return $this->redis->lRange($key, 0, -1);
  }

  public function zAdd(string $key, int $score, string $member) {
    return $this->redis->zAdd($key, $score, $member);
  }

  public function zRank(string $key, string $member) {
    return $this->redis->zRank($key, $member);
  }

  public function zRevRange(string $key, int $start, int $end, $options = null) {
    return $this->redis->zRevRange($key, $start, $end, $options);
  }

  public function unlink(string $key) {
    $this->redis->unlink($key);
  }

  public function redisQueryObject($model, $redisKey, $query, ...$queryParams) {
    if (is_callable($query)) {
      $data = $this->get($redisKey);
      
      if (!empty($userData)) {
        $data = json_decode($userData, true);
        return arrayToObject($data, $model);
      } else {
        $entry = $query(...$queryParams);
        if (!empty($entry)) {
          $this->set($redisKey, json_encode(objectToArray($entry)));
          return $entry;
        } else {
          return null;
        }
      }
    } else {
      return null;
    }
  
  }
  public function redisQueryObjectList($model, $redisKey, $query, ...$queryParams) {
    if (is_callable($query)) {
      $list = $this->getList($redisKey);
      if (!empty($list)) {
        foreach($list as $key => $data) {
          $list[$key] = arrayToObject(json_decode($data, true), $model);
        }
        return $list;
      } else {
        $result = $query(...$queryParams);
        if (!empty($result)) {
          foreach ($result as $entry) {
            $this->rPush($redisKey, json_encode(objectToArray($entry)));
          }
          return $result;
        } else {
          return [];
        }
      }
    } else {
      return [];
    }
  }

  public function redisQueryList($redisKey, $query, ...$queryParams) {
    if (is_callable($query)) {
      $list = $this->getList($redisKey);
      if (!empty($list)) {
        return $list;
      } else {
        $result = $query(...$queryParams);
        if (!empty($result)) {
          foreach ($result as $entry) {
            $this->rPush($redisKey, $entry);
          }
          return $result;
        } else {
          return [];
        }
      }
    } else {
      return [];
    }
  }
}