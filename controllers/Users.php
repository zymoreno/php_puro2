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
     * Renderiza una vista con extracción segura de variables.
     */
    private function renderView(string $relativePath, array $data = []): void
    {
        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }

        require_once __DIR__ . '/../' . ltrim($relativePath, '/');
    }

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
        return in_array($this->session, ['admin', 'seller'], true);
    }

    /* =========================
     * ROLES
     * ========================= */

    public function rolCreate(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $roles = (new User())->readRoles();

            $this->renderView('views/modules/users/rol_create.view.php', [
                'roles' => $roles
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rol = new User();
            $rol->setRolName($_POST['rol_name'] ?? '');
            $rol->createRol();

            header(self::REDIRECT_ROLREAD);
            exit;
        }

        http_response_code(405);
    }

    public function rolRead(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        $roles = (new User())->readRoles();

        $this->renderView('views/modules/users/rol_read.view.php', [
            'roles' => $roles
        ]);
    }

    public function rolUpdate(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $rol = (new User())->getRolByCode($_GET['idRol'] ?? null);

            $this->renderView('views/modules/users/rol_update.view.php', [
                'rol' => $rol
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rolUpdate = new User();
            $rolUpdate->setRolCode($_POST['rol_code'] ?? null);
            $rolUpdate->setRolName($_POST['rol_name'] ?? '');
            $rolUpdate->updateRol();

            header(self::REDIRECT_ROLREAD);
            exit;
        }

        http_response_code(405);
    }

    public function rolDelete(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        (new User())->deleteRol($_GET['idRol'] ?? null);

        header(self::REDIRECT_ROLREAD);
        exit;
    }

    /* =========================
     * USUARIOS
     * ========================= */

    public function userCreate(): void
    {
        if (!$this->isAdminOrSeller()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $roles = (new User())->readRoles();

            $this->renderView('views/modules/users/user_create.view.php', [
                'roles' => $roles
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User([
                'rolCode'      => $_POST['rol_code'] ?? null,
                'userCode'     => $_POST['user_code'] ?? null,
                'userName'     => $_POST['user_name'] ?? '',
                'userLastname' => $_POST['user_lastname'] ?? '',
                'userId'       => $_POST['user_id'] ?? '',
                'userEmail'    => $_POST['user_email'] ?? '',
                'userPass'     => $_POST['user_pass'] ?? '',
                'userState'    => $_POST['user_state'] ?? '',
            ]);

            $user->createUser();

            header(self::REDIRECT_USERREAD);
            exit;
        }

        http_response_code(405);
    }

    public function userRead(): void
    {
        if (!$this->isAdminOrSeller()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        $users = (new User())->readUsers();

        $this->renderView('views/modules/users/user_read.view.php', [
            'users' => $users
        ]);
    }

    public function userUpdate(): void
    {
        if (!$this->isAdminOrSeller()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $roles = (new User())->readRoles();
            $user  = (new User())->getUserByCode($_GET['idUser'] ?? null);

            $this->renderView('views/modules/users/user_update.view.php', [
                'roles' => $roles,
                'user'  => $user
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userUpdate = new User([
                'rolCode'      => $_POST['rol_code'] ?? null,
                'userCode'     => $_POST['user_code'] ?? null,
                'userName'     => $_POST['user_name'] ?? '',
                'userLastname' => $_POST['user_lastname'] ?? '',
                'userId'       => $_POST['user_id'] ?? '',
                'userEmail'    => $_POST['user_email'] ?? '',
                'userPass'     => $_POST['user_pass'] ?? '',
                'userState'    => $_POST['user_state'] ?? '',
            ]);

            $userUpdate->updateUser();

            header(self::REDIRECT_USERREAD);
            exit;
        }

        http_response_code(405);
    }

    public function userDelete(): void
    {
        if (!$this->isAdmin()) {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }

        (new User())->deleteUser($_GET['idUser'] ?? null);

        header(self::REDIRECT_USERREAD);
        exit;
    }
}
