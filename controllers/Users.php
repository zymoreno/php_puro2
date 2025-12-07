<?php

namespace App\Controllers;

use App\Models\User;

class Users
{
    private const REDIRECT_DASHBOARD = 'Location: ?c=Dashboard';
    private const REDIRECT_ROLREAD   = 'Location: ?c=Users&a=rolRead';
    private const REDIRECT_USERREAD  = 'Location: ?c=Users&a=userRead';

    private $session;

    public function __construct()
    {
        $this->session = $_SESSION['session'] ?? '';
    }

    /**
     * Renderiza una vista con extracción de variables opcional.
     */
    private function renderView(string $path, array $data = []): void
    {
        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }

        require_once $path;
    }

    // Controlador Principal
    public function main(): void
    {
        header(self::REDIRECT_DASHBOARD);
        exit;
    }

    // Crear Rol
    public function rolCreate(): void
    {
        if ($this->session === 'admin') {

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $roles = (new User())->read_roles();

                $this->renderView('views/modules/users/rol_create.view.php', [
                    'roles' => $roles
                ]);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $rol = new User();
                $rol->setRolName($_POST['rol_name'] ?? '');
                $rol->create_rol();

                header(self::REDIRECT_ROLREAD);
                exit;
            }

        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Consultar Roles
    public function rolRead(): void
    {
        if ($this->session === 'admin') {

            $roles = (new User())->read_roles();

            $this->renderView('views/modules/users/rol_read.view.php', [
                'roles' => $roles
            ]);

        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Actualizar Rol
    public function rolUpdate(): void
    {
        if ($this->session === 'admin') {

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $rol = (new User())->getrol_bycode($_GET['idRol'] ?? null);

                $this->renderView('views/modules/users/rol_update.view.php', [
                    'rol' => $rol
                ]);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $rolUpdate = new User();
                $rolUpdate->setRolCode($_POST['rol_code'] ?? null);
                $rolUpdate->setRolName($_POST['rol_name'] ?? '');
                $rolUpdate->update_rol();

                header(self::REDIRECT_ROLREAD);
                exit;
            }

        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Eliminar Rol
    public function rolDelete(): void
    {
        if ($this->session === 'admin') {

            (new User())->delete_rol($_GET['idRol'] ?? null);

            header(self::REDIRECT_USERREAD);
            exit;

        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Crear Usuario
    public function userCreate(): void
    {
        if ($this->session === 'admin' || $this->session === 'seller') {

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $roles = (new User())->read_roles();

                $this->renderView('views/modules/users/user_create.view.php', [
                    'roles' => $roles
                ]);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Consultar Usuarios
    public function userRead(): void
    {
        if ($this->session === 'admin' || $this->session === 'seller') {

            $users = (new User())->read_users();

            $this->renderView('views/modules/users/user_read.view.php', [
                'users' => $users
            ]);

        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Actualizar Usuario
    public function userUpdate(): void
    {
        if ($this->session === 'admin' || $this->session === 'seller') {

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {

                $roles = (new User())->read_roles();
                $user  = (new User())->getuser_bycode($_GET['idUser'] ?? null);

                $this->renderView('views/modules/users/user_update.view.php', [
                    'roles' => $roles,
                    'user'  => $user
                ]);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Eliminar Usuario
    public function userDelete(): void
    {
        if ($this->session === 'admin') {

            (new User())->delete_user($_GET['idUser'] ?? null);

            header(self::REDIRECT_USERREAD);
            exit;

        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }
}
