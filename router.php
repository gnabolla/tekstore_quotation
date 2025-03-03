<?php
// File path: C:\xampp\htdocs\tekstore_quotation\router.php

// Get the URI path
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Remove the base directory from the URI if it exists
$basePath = "/tekstore_quotation";
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// If the URI is empty, default to "/"
if (empty($uri)) {
    $uri = "/";
}

// Define available routes
$routes = [
  "/" => "controllers/index.php",
  
  // Agency routes
  "/agencies" => "controllers/agencies/index.php",
  "/agencies/create" => "controllers/agencies/create.php",
  "/agencies/store" => "controllers/agencies/store.php",
  "/agencies/edit" => "controllers/agencies/edit.php",
  "/agencies/update" => "controllers/agencies/update.php",
  "/agencies/delete" => "controllers/agencies/delete.php",
  
  // Quotation routes
  "/quotations" => "controllers/quotations/index.php",
  "/quotations/create" => "controllers/quotations/create.php",
  "/quotations/store" => "controllers/quotations/store.php",
  "/quotations/edit" => "controllers/quotations/edit.php",
  "/quotations/update" => "controllers/quotations/update.php",
  "/quotations/delete" => "controllers/quotations/delete.php",
  "/quotations/view" => "controllers/quotations/view.php",
  "/quotations/pdf" => "controllers/quotations/generate_pdf.php",
  "/quotations/update-status" => "controllers/quotations/update-status.php",
];

// Route the request to the appropriate controller
function routesToController(string $uri, array $routes): void
{
  if (array_key_exists($uri, $routes)) {
    require $routes[$uri];
  } else {
    http_response_code(404);
    require "controllers/404.php";
  }
}

// For debugging - uncomment this to see what URI is being processed
// echo "Processing URI: " . $uri;

routesToController($uri, $routes);