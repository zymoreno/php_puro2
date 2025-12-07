<?php
namespace App\Controllers;

use App\Models\User;

class Login {
    private const LOGIN_VIEW = "views/company/login.view.php";

    public function main() {

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            if (empty($_SESSION['session'])) {
                require_once self::LOGIN_VIEW;
            } else {
                header(self::REDIRECT_DASHBOARD);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $profile = new User(
                $_POST['user_email'],
                $_POST['user_pass']
            );

            $profile = $profile->login();

            if ($profile) {
                $active = $profile->getUserState();

                if ($active != 0) {
                    $_SESSION['session'] = $profile->getRolName();
                    $_SESSION['profile'] = serialize($profile);
                    header(self::REDIRECT_DASHBOARD);
                } else {
                    $message = "El Usuario NO está activo";
                    require_once self::LOGIN_VIEW;
                }

            } else {
                $message = "Credenciales incorrectas ó el Usuario NO existe";
                require_once self::LOGIN_VIEW;
            }
        }
    }
}
