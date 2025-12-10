<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\Landing;
use App\Controllers\Login;
use App\Controllers\Logout;
use App\Controllers\Dashboard;
use App\Controllers\Users;

// Lista blanca de controladores (NOMBRE => CLASE COMPLETA)
$allowedControllers = [
    'Landing'   => Landing::class,
    'Login'     => Login::class,
    'Logout'    => Logout::class,
    'Dashboard' => Dashboard::class,
    'Users'     => Users::class,
];

// Obtener controlador seguro
$controllerKey = $_GET['c'] ?? 'Landing';
$controllerKey = preg_replace('/\W/', '', $controllerKey);

// Si no existe en la whitelist, usar Landing
if (!array_key_exists($controllerKey, $allowedControllers)) {
    $controllerKey = 'Landing';
}

// Nombre de la clase con namespace
$controllerClass = $allowedControllers[$controllerKey];

// Crear instancia del controlador correcto
$controller = new $controllerClass();

// Acción segura
$action = $_GET['a'] ?? 'main';
$action = preg_replace('/\W/', '', $action);

// Vistas públicas (Landing y Login)
if ($controllerKey === 'Landing' || $controllerKey === 'Login') {

    require_once "views/company/header.view.php";

    if (is_callable([$controller, $action])) {
        call_user_func([$controller, $action]);
    }

    require_once "views/company/footer.view.php";

} elseif (!empty($_SESSION['session'])) {

    // Vistas por rol
    $profile     = isset($_SESSION['profile']) ? @unserialize($_SESSION['profile']) : null;
    $sessionRole = $_SESSION['session'];

    require_once "views/roles/" . $sessionRole . "/header.view.php";

    if (is_callable([$controller, $action])) {
        call_user_func([$controller, $action]);
    }

    require_once "views/roles/" . $sessionRole . "/footer.view.php";

} else {

    // Si intenta entrar sin sesión → redirigir a Landing
    header("Location: ?");
    exit;
}
