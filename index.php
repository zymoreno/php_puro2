<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/vendor/autoload.php';  // ÚNICO require_once para clases

use App\Controllers\Landing;
use App\Controllers\Login;
use App\Controllers\Logout;
use App\Controllers\Dashboard;
use App\Controllers\Users;

// 1) Lista blanca: controlador => clase (NO archivos)
$allowedControllers = [
    'Landing'   => Landing::class,
    'Login'     => Login::class,
    'Logout'    => Logout::class,
    'Dashboard' => Dashboard::class,
    'Users'     => Users::class,
];

// 2) Obtener controlador (por defecto Landing) y sanear
$controllerName = $_GET['c'] ?? 'Landing';
$controllerName = preg_replace('/\W/', '', (string)$controllerName);

// 3) Validar whitelist
if (!array_key_exists($controllerName, $allowedControllers)) {
    $controllerName = 'Landing';
}

// 4) Instanciar controlador por clase
$controllerClass = $allowedControllers[$controllerName];
$controller = new $controllerClass();

// 5) Acción segura (por defecto main) y sanear
$action = $_GET['a'] ?? 'main';
$action = preg_replace('/\W/', '', (string)$action);

// 6) Renderizado con control de sesión
if ($controllerName === 'Landing' || $controllerName === 'Login') {

    require __DIR__ . "/views/company/header.view.php";

    if (is_callable([$controller, $action])) {
        $controller->{$action}();
    }

    require __DIR__ . "/views/company/footer.view.php";

} elseif (!empty($_SESSION['session'])) {

    $sessionRole = (string)$_SESSION['session'];

    require __DIR__ . "/views/roles/{$sessionRole}/header.view.php";

    if (is_callable([$controller, $action])) {
        $controller->{$action}();
    }

    require __DIR__ . "/views/roles/{$sessionRole}/footer.view.php";

} else {
    header("Location: ?");
    exit;
}
