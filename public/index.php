<?php
require_once '../config/database.php';
session_start();

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$base_path = '/Subscribly/public';
$route = str_replace($base_path, '', $request);

// Remove query strings from route
if (strpos($route, '?') !== false) {
    $route = strstr($route, '?', true);
}

switch ($route) {
    case '/':
    case '':
        require '../templates/home.php';
        break;
    case '/login':
        require '../templates/login.php';
        break;
    case '/register':
        require '../templates/register.php';
        break;
    case '/dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Subscribly/public/login');
            exit;
        }
        require '../templates/dashboard.php';
        break;
    case '/subscription':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Subscribly/public/login');
            exit;
        }
        require '../templates/subscription-form.php';
        break;
    case '/subscription/delete':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Subscribly/public/login');
            exit;
        }
        require 'subscription/delete.php';
        break;
    case '/logout':
        require 'logout.php';
        break;
    default:
        http_response_code(404);
        require '../templates/404.php';
        break;
}
?>
