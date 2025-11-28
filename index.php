<?php
ob_start();
session_start();
// session_destroy();
require_once "models/DataBase.php";

// Lista blanca de controladores

$allowedControllers = [
    'Landing'   => 'controllers/Landing.php',
    'Login'     => 'controllers/Login.php',
    'Logout'    => 'controllers/Logout.php',
    'Dashboard' => 'controllers/Dashboard.php',
    'Users'     => 'controllers/Users.php',
];

// Obtener controlador de forma segura

$controllerName = isset($_GET['c']) ? $_GET['c'] : 'Landing';


$controllerName = preg_replace('/[^a-zA-Z0-9_]/', '', $controllerName);


if (!array_key_exists($controllerName, $allowedControllers)) {
    $controllerName = 'Landing';
}

// Incluir el archivo del controlador de forma segura
require_once $allowedControllers[$controllerName];

// Crear instancia del controlador (la clase se llama igual que la clave)
$controller = new $controllerName();
$view       = $controllerName;

// Obtener acción de forma segura

$action = isset($_GET['a']) ? $_GET['a'] : 'main';
$action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

// Lógica de carga de vistas

if ($view === 'Landing' || $view === 'Login') {
    // Vistas públicas (sin sesión)
    require_once "views/company/header.view.php";

    if (is_callable([$controller, $action])) {
        call_user_func([$controller, $action]);
    }

    require_once "views/company/footer.view.php";
} elseif (!empty($_SESSION['session'])) {
    // Vistas por rol con sesión iniciada
    require_once "models/User.php";

    $profile      = isset($_SESSION['profile']) ? @unserialize($_SESSION['profile']) : null;
    $sessionRole  = $_SESSION['session'];

    require_once "views/roles/" . $sessionRole . "/header.view.php";

    if (is_callable([$controller, $action])) {
        call_user_func([$controller, $action]);
    }

    require_once "views/roles/" . $sessionRole . "/footer.view.php";
} else {
    // Si no hay sesión ni vista pública válida, redirigir
    header("Location:?");
    exit;
}

ob_end_flush();
?>
