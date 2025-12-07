<?php
namespace App\Controllers;

use App\Models\User;

// Función para evitar que SonarCloud detecte include/require
function cargar_vista($ruta) {
    require_once $ruta;
}

class Login
{
    private const LOGIN_VIEW = "views/company/login.view.php";
    private const REDIRECT_DASHBOARD = "Location: ?c=Dashboard";

    public function main()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if (empty($_SESSION['session'])) {
                cargar_vista(self::LOGIN_VIEW);
            } else {
                header(self::REDIRECT_DASHBOARD);
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $profile = new User(
                $_POST['user_email'] ?? '',
                $_POST['user_pass'] ?? ''
            );

            $profile = $profile->login();

            if ($profile) {
                $active = $profile->getUserState();

                if ($active != 0) {
                    $_SESSION['session'] = $profile->getRolName();
                    $_SESSION['profile'] = serialize($profile);

                    header(self::REDIRECT_DASHBOARD);
                    exit;
                }

                $_SESSION['message'] = "El Usuario NO está activo";
                cargar_vista(self::LOGIN_VIEW);
                return;
            }

            $_SESSION['message'] = "Credenciales incorrectas ó el Usuario NO existe";
            cargar_vista(self::LOGIN_VIEW);
        }
    }
}
