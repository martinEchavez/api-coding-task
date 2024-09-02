<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Utils\Router;
use App\Controllers\CharacterController;
use App\Controllers\EquipmentController;
use App\Controllers\FactionController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Repositories\CharacterRepository;
use App\Repositories\EquipmentRepository;
use App\Repositories\FactionRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Middleware\AuthMiddleware;
use App\Utils\Response;

ob_start();

$router = new Router();

function addCrudRoutes(Router $router, string $basePath, $controller, array $middleware = []) {
    $router->addRoute('GET', $basePath, $controller, 'index', $middleware);
    $router->addRoute('GET', $basePath . '/{id}', $controller, 'show', $middleware);
    $router->addRoute('POST', $basePath, $controller, 'store', $middleware);
    $router->addRoute('PUT', $basePath . '/{id}', $controller, 'update', $middleware);
    $router->addRoute('DELETE', $basePath . '/{id}', $controller, 'destroy', $middleware);
}

// Initialize repositories
$repositories = [
    'character' => new CharacterRepository(),
    'equipment' => new EquipmentRepository(),
    'faction' => new FactionRepository(),
    'user' => new UserRepository(),
];

$services = [
    'auth' => new AuthService($repositories['user']),
];

$authMiddleware = new AuthMiddleware($services['auth']);

$controllers = [
    'character' => new CharacterController($repositories['character']),
    'equipment' => new EquipmentController($repositories['equipment']),
    'faction' => new FactionController($repositories['faction']),
    'user' => new UserController($repositories['user']),
    'auth' => new AuthController($services['auth']),
];

$resources = [
    'characters' => $controllers['character'],
    'equipments' => $controllers['equipment'],
    'factions' => $controllers['faction'],
    'users' => $controllers['user'],
];

foreach ($resources as $path => $controller) {
    addCrudRoutes($router, '/' . $path, $controller, [$authMiddleware]);
}

$router->addRoute('POST', '/login', $controllers['auth'], 'login');
$router->addRoute('POST', '/register', $controllers['auth'], 'register');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

try {
    $result = $router->handleRequest($method, $path);
    Response::send($result);
} catch (\Exception $e) {
    Response::send(['error' => 'An unexpected error occurred'], 500);
}

ob_end_flush();