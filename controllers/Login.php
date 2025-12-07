<?php
namespace App\Controllers;

use App\Models\User;

class Login
{
    private const LOGIN_VIEW = "views/company/login.view.php";
    private const REDIRECT_DASHBOARD = "Location: ?c=Dashboard";

    // Método para renderizar vistas (aceptado por Sonar)
    private function renderView(string $path): void
    {
        require_once $path;
    }

    public function main()
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
                $this->renderView(self::LOGIN_VIEW);
                return;
            }

            $_SESSION['message'] = "Credenciales incorrectas ó el Usuario NO existe";
            $this->renderView(self::LOGIN_VIEW);
        }
