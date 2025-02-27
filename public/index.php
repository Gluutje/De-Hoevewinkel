<?php
// Laad basis classes
require_once __DIR__ . '/../app/controllers/Controller.php';
require_once __DIR__ . '/../app/models/Model.php';

// Simpele router
$route = $_GET['route'] ?? 'home';

// Convert route naar controller/action
$parts = explode('/', $route);
$controllerName = ucfirst($parts[0]) . 'Controller';
$action = $parts[1] ?? 'index';

// Pad naar controller bestand
$controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";

// Check of controller bestaat
if (!file_exists($controllerFile)) {
    die("Controller {$controllerName} bestaat niet");
}

// Laad en instantieer controller
require_once $controllerFile;
$controller = new $controllerName();

// Check of action bestaat
if (!method_exists($controller, $action)) {
    die("Action {$action} bestaat niet in {$controllerName}");
}

// Voer action uit
$controller->$action(); 