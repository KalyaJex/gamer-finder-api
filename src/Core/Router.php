<?php

namespace App\Core;

class Router {
  private $routes = [];

  public function add($method, $path, $handler) {
    $path = preg_replace('/{(\w+)}/', '(?P<$1>[^/]+)', $path);
    $this->routes[] = [
      'method' => $method,
      'path' => '#^' . $path . '$#',
      'handler' => $handler
    ];
  }

  public function dispatch($method, $path) {
    foreach ($this->routes as $route) {
      if ($route['method'] === $method && preg_match($route['path'], $path, $matches)) {
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        call_user_func_array($route['handler'], $params);
        return;
      }
    }

    // If no route matches, return 404 response
    header("HTTP/1.0 404 Not Found");
    echo '404 Not Found';
  }
}