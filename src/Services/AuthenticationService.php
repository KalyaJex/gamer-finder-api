<?php

namespace App\Services;

use App\Models\Token;
use App\Repositories\TokenRepository;
use App\Repositories\UserRepository;

class AuthenticationService {

  public function __construct(
    private UserRepository $userRepository,
    private TokenRepository $tokenRepository) {}

  public function authenticateUser(string $email, string $password) {

    $user = $this->userRepository->findByEmail($email);

    if(!empty($user) && password_verify($password, $user->password)) {
      if ($user->is_email_confirmed) {
        $accessToken = generateAccessToken($user->id);

        $refreshToken = new Token($user->id);
        $allUserTokens = $this->tokenRepository->findAllByUserId($user->id) ?? [];
        $releventTokens = array_filter($allUserTokens, function($token) use($refreshToken) {
          return $token->user_agent === $refreshToken->user_agent;
        });

        if (empty($releventTokens)) {
          $this->tokenRepository->create($refreshToken);
          setcookie('refresh-token', $refreshToken->token, strtotime($refreshToken->created) + $_ENV['REFRESH_TOKEN_EXP'], httponly: true, path: '/');
        } else {
          setcookie('refresh-token', $releventTokens[0]->token, strtotime($releventTokens[0]->created) + $_ENV['REFRESH_TOKEN_EXP'], httponly: true, path: '/');
        }

        return [
          'status' => 200,
          'body' => [
            'accessToken' => $accessToken, 
          ]
        ];
      } else {
        return [
          'status' => 403,
          'body' => [
            'error' => 'EmailNotConfirmed',
            'message' => 'You need to confirm your email address before logging in. Please check your email for the confirmation link.',
          ]
        ];
      }
    } else {
      return [
        'status' => 401,
        'body' => [
          'error' => 'InvalidCredentials',
          'message' => 'Invalid credentials'
        ]
      ];
    }
  }

  public function refreshToken(string $token, string $userId) {
    if (!empty($token)) {
      $refreshToken = $this->tokenRepository->findByToken($token);
      if (!empty($refreshToken) && $refreshToken->user_id === $userId) {
        if (validateTokenExpTime($refreshToken->created)) {
          $accessToken = generateAccessToken($userId);
          return [
            'status' => 200,
            'body' => [
              'accessToken' => $accessToken, 
            ]
          ];
        } else {
          return [
            'status' => 400,
            'body' => [
              'error' => 'InvalidToken',
              'message' => 'Token expired'
            ]
          ];
        }
      } else {
        return [
          'status' => 400,
          'body' => [
            'error' => 'TokenNotFound',
            'message' => 'Refresh token not found'
          ]
        ];
      }
    } else {
      return [
        'status' => 400,
        'body' => [
          'error' => 'TokenNotFound',
          'message' => 'Refresh token not found'
        ]
      ];
    }
  }

  public function deleteRefreshToken(string $userId, string $userAgent) {
    $tokens = $this->tokenRepository->findAllByUserId($userId);
    $refreshToken = null;
    foreach ($tokens as $token) {
      if ($userAgent === $token->user_agent) {
        $refreshToken = $token;
        break;
      }
    }

    if (!empty($refreshToken)) {
      if ($this->tokenRepository->delete($refreshToken->token)) {
        return [
          'status' => 200,
        ];
      } else {
        return [
          'status' => 500, 
          'body' => [
            'error' => 'InternalServerError',
            'message' => 'Internal Server Error'
          ]
        ];
      }
    } else {
      return [
        'status' => 400,
        'body' => [
          'error' => 'TokenNotFound',
          'message' => 'Refresh token not found'
        ]
      ];
    }
  }
}