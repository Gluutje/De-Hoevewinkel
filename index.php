<?php
// Error reporting voor development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basis classes laden
require_once __DIR__ . '/app/controllers/Controller.php';
require_once __DIR__ . '/app/models/Model.php';

// Route bepalen
$route = $_GET['route'] ?? 'home';

// Route naar controller/action omzetten
$parts = explode('/', $route);
$controllerName = ucfirst($parts[0]) . 'Controller';
$action = $parts[1] ?? 'index';

// Pad naar controller bestand
$controllerFile = __DIR__ . "/app/controllers/{$controllerName}.php";

// Check of controller bestaat
if (!file_exists($controllerFile)) {
    die("Controller {$controllerName} bestaat niet");
}

// Controller laden en instantiëren
require_once $controllerFile;
$controller = new $controllerName();

// Check of action bestaat
if (!method_exists($controller, $action)) {
    die("Action {$action} bestaat niet in {$controllerName}");
}

// Action uitvoeren
$controller->$action(); 