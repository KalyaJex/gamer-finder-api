<?php

namespace App\Services;

use App\Models\Game;
use App\Models\UserGame;
use App\Repositories\UserRepository;
use App\Repositories\GameRepository;
use App\Repositories\UserGameRepository;

class UsersService {

  public function __construct(
    private UserRepository $userRepository,
    private GameRepository $gameRepository,
    private UserGameRepository $userGameRepository,
    private RedisService $redisService) {}

  public function getSimilarUsers(int $userId, int $from, int $to) {
   $similarUsers = $this->redisService->zRevRange("user:$userId:similarities", $from, $to, true);

    if (empty($similarUsers)) {
      $similarUsers = $this->computeAndStoreSimilarities($userId, $from, $to);

    }
    return [
      'status' => 200,
      'body' => [
        'users' => $similarUsers
      ]
    ];

  }

  public function computeAndStoreSimilarities(int $userId, int $from, int $to): array {
    $similarUsers = [];
    $userGameLibrary = $this->userGameRepository->getGamesByUserId($userId);

    if (empty($userGameLibrary)) {
      return [];
    }
    $allUserIds = $this->userRepository->getAllUserIds();

    foreach ($allUserIds as $otherUserId) {
      if ($userId !== $otherUserId) {
        $similarityScore1 = $this->redisService->zRank("user:$userId:similarities",  $otherUserId);
        $similarityScore2 = $this->redisService->zRank("user:$otherUserId:similarities",  $userId);

        $isAllEmpty = empty($similarityScore1) && empty($similarityScore2);
        $isDifferent = (!empty($similarityScore1) && !empty($similarityScore2)) && $similarityScore1 !== $similarityScore2;
        $similarityScore = 0;
        if ($isAllEmpty || $isDifferent) {
          $otherUserGameLibrary = $this->userGameRepository->getGamesByUserId($otherUserId);
          $similarityScore = jaccardIndex($userGameLibrary, $otherUserGameLibrary);
          $this->redisService->zAdd("user:$userId:similarities", $similarityScore, $otherUserId);
          $this->redisService->zAdd("user:$otherUserId:similarities", $similarityScore, $userId);
        } else if (empty($similarityScore1)) {
          $this->redisService->zAdd("user:$otherUserId:similarities", $similarityScore1, $userId);
          $similarityScore = $similarityScore1;
        } else if (empty($similarityScore2)) {
          $this->redisService->zAdd("user:$userId:similarities", $similarityScore2, $otherUserId);
          $similarityScore = $similarityScore2;
        }

        $similarUsers[$otherUserId] = $similarityScore;
      }
    }
    arsort($similarUsers);

    return array_slice($similarUsers, $from, $to - $from, true);
  }

  private function getSimilarityKey(int $userId1, int $userId2): string
    {
        return $userId1 < $userId2 ? "similarity:$userId1:$userId2" : "similarity:$userId2:$userId1";
    }
}