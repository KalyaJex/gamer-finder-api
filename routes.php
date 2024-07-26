<?php 

$router = $container->get('router');
$BASE_URI = '/gamer-finder-api/';

// Login endpoint

$router->add('POST', $BASE_URI . 'login', function() use($container) {
  $authController = $container->get('authController');

  $data = json_decode(file_get_contents('php://input'), true);

  $validationResult  = validateRequiredFields($data, ['password', 'email']);
  if (!$validationResult ['isValid']) {
    header("HTTP/1.0 400 Bad Request");
    echo json_encode(['error' => 'Email and/or password are required']);
    return;
  }

  $authResult = $authController->authenticateUser($data['email'], $data['password']);

  if ($authResult['authenticated']) {
    echo json_encode([
      'accessToken' => $authResult['accessToken'],
      'refreshToken' => $authResult['refreshToken'],
      'message' => 'Login successful'
    ]);
  } else {
    header("HTTP/1.0 401 Unauthorized");
    echo json_encode(['error' => 'Invalid credentials']);
  }

});

// refresh token endpoint

$router->add('POST',  $BASE_URI . 'refresh', function() use($container) {
  $authController = $container->get('authController');
  $data = json_decode(file_get_contents('php://input'), true);
  if (!isset($data['refresh_token'])) {
      header("HTTP/1.0 400 Bad Request");
      echo json_encode(['error' => 'Refresh token is required']);
      return;
  }

  $key = $_ENV['REFRESH_KEY'];
  $decoded = $authController->validateToken($data['refresh_token'], $key);
  if ($decoded) {
      $userId = $decoded['user_id'];
      $newAccessToken = $authController->generateAccessToken($userId);
      echo json_encode(['access_token' => $newAccessToken]);
  } else {
      header("HTTP/1.0 401 Unauthorized");
      echo json_encode(['error' => 'Invalid refresh token']);
  }
});

$router->add('POST',  $BASE_URI . 'register', function() use($container) {
  $userController = $container->get('userController');
  $authController = $container->get('authController');

  $data = json_decode(file_get_contents('php://input'), true);

  $validationResult  = validateRequiredFields($data, ['password', 'username']);

  if (!$validationResult ['isValid']) {
    header("HTTP/1.0 400 Bad Request");
    echo json_encode(['error' => 'Email and/or password are required']);
    return;
  }

  

  $result = $userController->create($data['username'], $data['email'], $data['password']);

  if (array_key_exists('error', $result)) {
    header("HTTP/1.0 409 Conflict");
    echo json_encode(['error' => $result['error']]);
    return;
  }


  if (!empty($result['userId'])) {
    $accessToken = $authController->generateAccessToken($result['userId']);
    $refreshToken  = $authController->generateRefreshToken($result['userId']);

    echo json_encode([
      'message' => 'Registration successful',
      'access_token' => $accessToken,
      'refresh_token' => $refreshToken
  ]);
  }

});


$method = $_SERVER['REQUEST_METHOD'];

$path = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $path);
