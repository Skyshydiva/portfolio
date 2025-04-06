<?php
session_start();

// Include necessary files
require_once __DIR__ . '/../app/core/SessionManager.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/BugController.php';

// Include routes
require_once __DIR__ . '/routes.php';

// Dispatch the request
$uri = parse_url($_SERVER['REQUEST_URI']);
$uri = isset($uri['query']) ? $uri['query'] : "/";
$router->dispatch($uri);

?>