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
     * Método para cargar vistas.
     */
    private function renderView(string $path): void
    {
        require_once $path;
    }

    // Controlador Principal
    public function main(): void
    {
        header(self::REDIRECT_DASHBOARD);
        exit;
    }

    // Controlador Crear Rol
    public function rolCreate(): void
    {
        if ($this->session === 'admin') {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $this->renderView('views/modules/users/rol_create.view.php');
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

    // Controlador Consultar Roles
    public function rolRead(): void
    {
        if ($this->session === 'admin') {
            $roles = new User();
            $roles = $roles->read_roles();
            $this->renderView('views/modules/users/rol_read.view.php');
        } else {
            header(self::REDIRECT_DASHBOARD); // corregido hheader
            exit;
        }
    }

    // Controlador Actualizar Rol
    public function rolUpdate(): void
    {
        if ($this->session === 'admin') {

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $rolId = new User();
                $rolId = $rolId->getrol_bycode($_GET['idRol'] ?? '');
                $this->renderView('views/modules/users/rol_update.view.php');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $rolUpdate = new User();
                $rolUpdate->setRolCode($_POST['rol_code'] ?? '');
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

    // Controlador Eliminar Rol
    public function rolDelete(): void
    {
        if ($this->session === 'admin') {
            $rol = new User();
            $rol->delete_rol($_GET['idRol'] ?? '');
            header(self::REDIRECT_ROLREAD);
            exit;
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Crear Usuario
    public function userCreate(): void
    {
        if ($this->session === 'admin' || $this->session === 'seller') {

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $roles = new User();
                $roles = $roles->read_roles();
                $this->renderView('views/modules/users/user_create.view.php');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user = new User(
                    $_POST['rol_code']     ?? '',
                    null,
                    $_POST['user_name']    ?? '',
                    $_POST['user_lastname']?? '',
                    $_POST['user_id']      ?? '',
                    $_POST['user_email']   ?? '',
                    $_POST['user_pass']    ?? '',
                    $_POST['user_state']   ?? ''
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

    // Controlador Consultar Usuarios
    public function userRead(): void
    {
        if ($this->session === 'admin' || $this->session === 'seller') {
            $state = ['Inactivo', 'Activo'];
            $users = new User();
            $users = $users->read_users();
            $this->renderView('views/modules/users/user_read.view.php');
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Actualizar Usuario
    public function userUpdate(): void
    {
        if ($this->session === 'admin' || $this->session === 'seller') {

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $state = ['Inactivo', 'Activo'];
                $roles = new User();
                $roles = $roles->read_roles();

                $user = new User();
                $user = $user->getuser_bycode($_GET['idUser'] ?? '');

                $this->renderView('views/modules/users/user_update.view.php');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userUpdate = new User(
                    $_POST['rol_code']     ?? '',
                    $_POST['user_code']    ?? '',
                    $_POST['user_name']    ?? '',
                    $_POST['user_lastname']?? '',
                    $_POST['user_id']      ?? '',
                    $_POST['user_email']   ?? '',
                    $_POST['user_pass']    ?? '',
                    $_POST['user_state']   ?? ''
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

    // Controlador Eliminar Usuario
    public function userDelete(): void
    {
        if ($this->session === 'admin') {
            $user = new User();
            $user->delete_user($_GET['idUser'] ?? '');
            header(self::REDIRECT_USERREAD);
            exit;
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }
}
