<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class Login
{
    private const LOGIN_VIEW = __DIR__ . '/../views/company/login.view.php';
    private const REDIRECT_DASHBOARD = 'Location: ?c=Dashboard';

    /**
     * Renderiza una vista.
     * Las vistas no se cargan por autoload (PSR-4 no aplica).
     */
    private function renderView(string $absolutePath): void
    {
        // NOSONAR - Inclusión intencional de vistas
        require_once $absolutePath;
    }

    public function main(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'GET') {
            if (empty($_SESSION['session'])) {
                $this->renderView(self::LOGIN_VIEW);
                return;
            }

            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        if ($method === 'POST') {
            $email = $_POST['user_email'] ?? '';
            $pass  = $_POST['user_pass'] ?? '';

            $profile = (new User($email, $pass))->login();

            if ($profile) {
                $active = (int) $profile->getUserState();

                if ($active !== 0) {
                    $_SESSION['session'] = $profile->getRolName();
                    $_SESSION['profile'] = serialize($profile);

                    header(self::REDIRECT_DASHBOARD);
                    exit;
                }

                $_SESSION['message'] = 'El Usuario NO está activo';
                $this->renderView(self::LOGIN_VIEW);
                return;
            }

            $_SESSION['message'] = 'Credenciales incorrectas o el Usuario NO existe';
            $this->renderView(self::LOGIN_VIEW);
            return;
        }

        // Método HTTP no permitido
        http_response_code(405);
    }
}
