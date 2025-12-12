<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class Users
{
    private const REDIRECT_DASHBOARD = 'Location: ?c=Dashboard';
    private const REDIRECT_ROLREAD   = 'Location: ?c=Users&a=rolRead';
    private const REDIRECT_USERREAD  = 'Location: ?c=Users&a=userRead';

    private string $session;

    public function __construct()
    {
        $this->session = (string)($_SESSION['session'] ?? '');
    }

    /**
     * Renderiza una vista con extracción de variables opcional.
     */
    private function renderView(string $relativePath, array $data = []): void
    {
        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }

        // Ruta absoluta para evitar fallos por ruta relativa
        require __DIR__ . '/../' . ltrim($relativePath, '/');
    }

    // Controlador Principal
    public function main(): void
    {
        header(self::REDIRECT_DASHBOARD);
        exit;
    }

    private function isAdmin(): bool
    {
        return $this->session === 'admin';
    }

    private function isAdminOrSeller(): bool
    {
        return $this->session === 'admin' || $this->session === 'seller';
    }

    // Crear Rol
    public function rolCreate(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'GET') {
            $roles = (new User())->read_roles();

            $this->renderView('views/modules/users/rol_create.view.php', [
                'roles' => $roles
            ]);
            return;
        }

        if ($method === 'POST') {
            $rol = new User();
            $rol->setRolName($_POST['rol_name'] ?? '');
            $rol->create_rol();

            header(self::REDIRECT_ROLREAD);
            exit;
        }

        http_response_code(405);
    }

    // Consultar Roles
    public function rolRead(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        $roles = (new User())->read_roles();

        $this->renderView('views/modules/users/rol_read.view.php', [
            'roles' => $roles
        ]);
    }

    // Actualizar Rol
    public function rolUpdate(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'GET') {
            $rol = (new User())->getrol_bycode($_GET['idRol'] ?? null);

            $this->renderView('views/modules/users/rol_update.view.php', [
                'rol' => $rol
            ]);
            return;
        }

        if ($method === 'POST') {
            $rolUpdate = new User();
            $rolUpdate->setRolCode($_POST['rol_code'] ?? null);
            $rolUpdate->setRolName($_POST['rol_name'] ?? '');
            $rolUpdate->update_rol();

            header(self::REDIRECT_ROLREAD);
            exit;
        }

        http_response_code(405);
    }

    // Eliminar Rol
    public function rolDelete(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        (new User())->delete_rol($_GET['idRol'] ?? null);

        // CORRECCIÓN: después de borrar rol debe ir a rolRead
        header(self::REDIRECT_ROLREAD);
        exit;
    }

    // Crear Usuario
    public function userCreate(): void
    {
        if (!$this->isAdminOrSeller()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'GET') {
            $roles = (new User())->read_roles();

            $this->renderView('views/modules/users/user_create.view.php', [
                'roles' => $roles
            ]);
            return;
        }

        if ($method === 'POST') {
            $user = new User(
                $_POST['rol_code']      ?? null,
                null,
                $_POST['user_name']     ?? '',
                $_POST['user_lastname'] ?? '',
                $_POST['user_id']       ?? '',
                $_POST['user_email']    ?? '',
                $_POST['user_pass']     ?? '',
                $_POST['user_state']    ?? ''
            );

            $user->create_user();

            header(self::REDIRECT_USERREAD);
            exit;
        }

        http_response_code(405);
    }

    // Consultar Usuarios
    public function userRead(): void
    {
        if (!$this->isAdminOrSeller()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        $users = (new User())->read_users();

        $this->renderView('views/modules/users/user_read.view.php', [
            'users' => $users
        ]);
    }

    // Actualizar Usuario
    public function userUpdate(): void
    {
        if (!$this->isAdminOrSeller()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'GET') {
            $roles = (new User())->read_roles();
            $user  = (new User())->getuser_bycode($_GET['idUser'] ?? null);

            $this->renderView('views/modules/users/user_update.view.php', [
                'roles' => $roles,
                'user'  => $user
            ]);
            return;
        }

        if ($method === 'POST') {
            $userUpdate = new User(
                $_POST['rol_code']      ?? null,
                $_POST['user_code']     ?? null,
                $_POST['user_name']     ?? '',
                $_POST['user_lastname'] ?? '',
                $_POST['user_id']       ?? '',
                $_POST['user_email']    ?? '',
                $_POST['user_pass']     ?? '',
                $_POST['user_state']    ?? ''
            );

            $userUpdate->update_user();

            header(self::REDIRECT_USERREAD);
            exit;
        }

        http_response_code(405);
    }

    // Eliminar Usuario
    public function userDelete(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        (new User())->delete_user($_GET['idUser'] ?? null);

        header(self::REDIRECT_USERREAD);
        exit;
    }
}
