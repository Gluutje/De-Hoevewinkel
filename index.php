<?php
require_once 'config.php';
require_once 'app/models/LoginModel.php';
require_once 'app/controllers/LoginController.php';
require_once 'app/controllers/DashboardController.php';
require_once 'app/controllers/FamilieController.php';
require_once 'app/controllers/FamilielidController.php';
require_once 'app/controllers/contributie/ContributieBaseController.php';
require_once 'app/controllers/contributie/ContributieListController.php';
require_once 'app/controllers/contributie/ContributieInstellingenController.php';
require_once 'app/controllers/BoekjaarController.php';
require_once 'app/controllers/contributie/ContributieBetalingController.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/'; // Pas dit aan als je project in een subdirectory staat

// Verwijder de base path van de request URI
$uri = substr($request_uri, strlen($base_path));

// Verwijder query string als die er is
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = trim($uri, '/');

// Eenvoudige routing
switch ($uri) {
    case '':
    case 'login':
        $controller = new LoginController($db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->index();
        }
        break;
    case 'dashboard':
        $controller = new DashboardController($db);
        $controller->index();
        break;
    case 'dashboard/secretaris':
        $controller = new DashboardController($db);
        $controller->secretaris();
        break;
    case 'dashboard/penningmeester':
        $controller = new DashboardController($db);
        $controller->penningmeester();
        break;
    case 'families':
        $controller = new FamilieController($db);
        $controller->index();
        break;
    case 'families/add':
        $controller = new FamilieController($db);
        $controller->add();
        break;
    case (preg_match('/^families\/edit\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new FamilieController($db);
        $controller->edit($matches[1]);
        break;
    case (preg_match('/^families\/delete\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new FamilieController($db);
        $controller->delete($matches[1]);
        break;
    case 'familieleden':
        $controller = new FamilielidController($db);
        $controller->index();
        break;
    case 'familieleden/add':
        $controller = new FamilielidController($db);
        $controller->add();
        break;
    case (preg_match('/^familieleden\/edit\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new FamilielidController($db);
        $controller->edit($matches[1]);
        break;
    case (preg_match('/^familieleden\/delete\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new FamilielidController($db);
        $controller->delete($matches[1]);
        break;
    case 'logout':
        $controller = new LoginController($db);
        $controller->logout();
        break;
    case 'contributies':
        $controller = new ContributieListController($db);
        $controller->handle();
        break;
    case 'contributies/add':
        $controller = new ContributieController($db);
        $controller->add();
        break;
    case (preg_match('/^contributies\/edit\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new ContributieController($db);
        $controller->edit($matches[1]);
        break;
    case (preg_match('/^contributies\/delete\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new ContributieController($db);
        $controller->delete($matches[1]);
        break;
    case 'contributies/instellingen':
        $controller = new ContributieInstellingenController($db);
        $controller->handle();
        break;
    case (preg_match('/^families\/members\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new FamilieController($db);
        $controller->viewMembers($matches[1]);
        break;
    case 'boekjaren':
        $controller = new BoekjaarController($db);
        $controller->index();
        break;
    case 'boekjaren/add':
        $controller = new BoekjaarController($db);
        $controller->add();
        break;
    case (preg_match('/^boekjaren\/edit\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new BoekjaarController($db);
        $controller->edit($matches[1]);
        break;
    case (preg_match('/^boekjaren\/delete\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new BoekjaarController($db);
        $controller->delete($matches[1]);
        break;
    case (preg_match('/^boekjaren\/setActief\/(\d+)$/', $uri, $matches) ? true : false):
        $controller = new BoekjaarController($db);
        $controller->setActief($matches[1]);
        break;
    case 'contributies/overzicht':
        $controller = new ContributieListController($db);
        $controller->overzicht();
        break;
    case 'contributies/verwerk-betaling':
        $controller = new ContributieBetalingController($db);
        $controller->verwerkBetaling();
        break;
    default:
        // 404 pagina
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
        break;
}

