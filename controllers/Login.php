<?php
namespace App\Controllers;

use App\Models\User;

class Login
{
    private const LOGIN_VIEW = "views/company/login.view.php";
    private const REDIRECT_DASHBOARD = "Location: ?c=Dashboard";

    // Método para renderizar vistas
    private function renderView(string $path): void
    {
        require_once $path;
    }

    public function main(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if (empty($_SESSION['session'])) {
                $this->renderView(self::LOGIN_VIEW);
            } else {
                header(self::REDIRECT_DASHBOARD);
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = $_POST['user_email'] ?? '';
            $pass  = $_POST['user_pass'] ?? '';

            $profile = new User($email, $pass);
            $profile = $profile->login();

            // 1. ¿Encontró usuario?
            if ($profile) {
                $active = (int) $profile->getUserState();

                // 1.1 Usuario activo
                if ($active !== 0) {
                    $_SESSION['session'] = $profile->getRolName();
                    $_SESSION['profile'] = serialize($profile);

                    header(self::REDIRECT_DASHBOARD);
                    exit;
                }

                // 1.2 Usuario NO activo
                $_SESSION['message'] = "El Usuario NO está activo";
                $this->renderView(self::LOGIN_VIEW);
                return;
            }

            // 2. Credenciales incorrectas o usuario no existe
            $_SESSION['message'] = "Credenciales incorrectas ó el Usuario NO existe";
            $this->renderView(self::LOGIN_VIEW);
            return;
        }
    }
}
