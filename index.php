<?php
session_start();

// Lista blanca de controladores y sus rutas
$allowedControllers = [
    'Landing'   => 'controllers/Landing.php',
    'Login'     => 'controllers/Login.php',
    'Logout'    => 'controllers/Logout.php',
    'Dashboard' => 'controllers/Dashboard.php',
    'Users'     => 'controllers/Users.php',
];

// Obtener controlador (por defecto Landing)
$controllerName = isset($_GET['c']) ? $_GET['c'] : 'Landing';
$controllerName = preg_replace('/\W/', '', $controllerName);

// Validar en la whitelist
if (!array_key_exists($controllerName, $allowedControllers)) {
    $controllerName = 'Landing';
}

// Cargar el archivo del controlador
require_once $allowedControllers[$controllerName];

// IMPORTANTE: construir el nombre de la clase con namespace
$controllerClass = "App\\Controllers\\{$controllerName}";
$controller      = new $controllerClass();

// Acción segura (por defecto main)
$action = isset($_GET['a']) ? $_GET['a'] : 'main';
$action = preg_replace('/\W/', '', $action);

// VISTAS PÚBLICAS (Landing y Login)
if ($controllerName === 'Landing' || $controllerName === 'Login') {

    require_once "views/company/header.view.php";

    if (is_callable([$controller, $action])) {
        call_user_func([$controller, $action]);
    }

    require_once "views/company/footer.view.php";

} elseif (!empty($_SESSION['session'])) {

    // VISTAS POR ROL (cuando ya hay sesión)
    $profile     = isset($_SESSION['profile']) ? @unserialize($_SESSION['profile']) : null;
    $sessionRole = $_SESSION['session'];

    require_once "views/roles/" . $sessionRole . "/header.view.php";

    if (is_callable([$controller, $action])) {
        call_user_func([$controller, $action]);
    }

    require_once "views/roles/" . $sessionRole . "/footer.view.php";

} else {
    // Si intenta entrar a algo privado sin sesión → volver a inicio
    header("Location: ?");
    exit;
}
