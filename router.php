<?php
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$routes = [
  "/" => "controllers/index.php",
];

function routesToController(string $uri, array $routes): void
{
  if (array_key_exists($uri, $routes)) {
    require $routes[$uri];
  } else {
    http_response_code(404);
    echo "404 Not Found";
  }
}

routesToController($uri, $routes);
