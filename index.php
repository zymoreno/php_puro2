<?php

use App\Controllers\Landing;
use App\Controllers\Login;
use App\Controllers\Logout;
use App\Controllers\Dashboard;
use App\Controllers\Users;

session_start();

// Lista blanca de controladores y sus rutas
$allowedControllers = [
    'Landing'   => 'controllers/Landing.php',
    'Login'     => 'controllers/Login.php',
    'Logout'    => 'controllers/Logout.php',
    'Dashboard' => 'controllers/Dashboard.php',
    'Users'     => 'controllers/Users.php',
];

// Obtener controlador seguro
$controllerName = isset($_GET['c']) ? $_GET['c'] : 'Landing';
$controllerName = preg_replace('/[^a-zA-Z0-9_]/', '', $controllerName);

// Validar en whitelist
if (!array_key_exists($controllerName, $allowedControllers)) {
    $controllerName = 'Landing';
}

// Cargar controlador desde su archivo (solo una vez)
require_once $allowedControllers[$controllerName];

// Crear instancia
$controller = new $controllerName();

// Acción segura
$action = isset($_GET['a']) ? $_GET['a'] : 'main';
$action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

// Carga de vistas (públicas o por roles)
if ($controllerName === 'Landing' || $controllerName === 'Login') {

    // Vistas públicas
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

    // Si intenta entrar sin sesión → redirigir
    header("Location: ?");
    exit;
}

?>
