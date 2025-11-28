<?php
require_once "models/User.php";

class Users {
    private $sessionRole;
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

      
        $this->sessionRole = isset($_SESSION['session']) ? $_SESSION['session'] : null;
    }

    // Redirección al módulo principal (Dashboard)     
    public function main()
    {
        header("Location: ?c=Dashboard");
        exit;
    }
     // Verifica si el usuario actual tiene rol administrador     
    private function isAdmin()
    {
        return $this->sessionRole === 'admin';
    }

    // Verifica si el usuario actual es admin o seller     
    private function isAdminOrSeller()
    {
        return $this->sessionRole === 'admin' || $this->sessionRole === 'seller';
    }

    // Controlador Crear Rol
     
    public function rolCreate()
    {
        if (!$this->isAdmin()) {
            header("Location: ?c=Dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once "views/modules/users/rol_create.view.php";

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rolName = isset($_POST['rol_name']) ? trim($_POST['rol_name']) : '';

            if ($rolName !== '') {
                $rol = new User();
                $rol->setRolName($rolName);
                $rol->create_rol();
            }

            header("Location: ?c=Users&a=rolRead");
            exit;
        }
    }

    // Controlador Consultar Roles     
    public function rolRead()
    {
        if (!$this->isAdmin()) {
            header("Location: ?c=Dashboard");
            exit;
        }

        $rolesModel = new User();
        $roles = $rolesModel->read_roles();

        require_once "views/modules/users/rol_read.view.php";
    }

    // Controlador Actualizar Rol
     
    public function rolUpdate()
    {
        if (!$this->isAdmin()) {
            header("Location: ?c=Dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['idRol'])) {
                header("Location: ?c=Users&a=rolRead");
                exit;
            }

            $rolModel = new User();
            $rolId = $rolModel->getrol_bycode($_GET['idRol']);

            if (!$rolId) {
                header("Location: ?c=Users&a=rolRead");
                exit;
            }

            require_once "views/modules/users/rol_update.view.php";

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rolCode = isset($_POST['rol_code']) ? $_POST['rol_code'] : null;
            $rolName = isset($_POST['rol_name']) ? trim($_POST['rol_name']) : '';

            if ($rolCode !== null && $rolName !== '') {
                $rolUpdate = new User();
                $rolUpdate->setRolCode($rolCode);
                $rolUpdate->setRolName($rolName);
                $rolUpdate->update_rol();
            }

            header("Location: ?c=Users&a=rolRead");
            exit;
        }
    }

    // Controlador Eliminar Rol
     
    public function rolDelete()
    {
        if (!$this->isAdmin()) {
            header("Location: ?c=Dashboard");
            exit;
        }

        if (isset($_GET['idRol'])) {
            $rol = new User();
            $rol->delete_rol($_GET['idRol']);
        }

        header("Location: ?c=Users&a=rolRead");
        exit;
    }

    // Controlador Crear Usuario
     
    public function userCreate()
    {
        if (!$this->isAdminOrSeller()) {
            header("Location: ?c=Dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $rolesModel = new User();
            $roles = $rolesModel->read_roles();

            require_once "views/modules/users/user_create.view.php";

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User(
                $_POST['rol_code'] ?? null,
                null,
                $_POST['user_name'] ?? '',
                $_POST['user_lastname'] ?? '',
                $_POST['user_id'] ?? '',
                $_POST['user_email'] ?? '',
                $_POST['user_pass'] ?? '',
                $_POST['user_state'] ?? ''
            );

            $user->create_user();
            header("Location: ?c=Users&a=userRead");
            exit;
        }
    }
    //  Controlador Consultar Usuarios
     
    public function userRead()
    {
        if (!$this->isAdminOrSeller()) {
            header("Location: ?c=Dashboard");
            exit;
        }

        $state = ['Inactivo', 'Activo'];

        $usersModel = new User();
        $users = $usersModel->read_users();

        require_once "views/modules/users/user_read.view.php";
    }
    // Controlador Actualizar Usuario
     
    public function userUpdate()
    {
        if (!$this->isAdminOrSeller()) {
            header("Location: ?c=Dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['idUser'])) {
                header("Location: ?c=Users&a=userRead");
                exit;
            }

            $state = ['Inactivo', 'Activo'];

            $rolesModel = new User();
            $roles = $rolesModel->read_roles();

            $userModel = new User();
            $user = $userModel->getuser_bycode($_GET['idUser']);

            if (!$user) {
                header("Location: ?c=Users&a=userRead");
                exit;
            }

            require_once "views/modules/users/user_update.view.php";

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userUpdate = new User(
                $_POST['rol_code'] ?? null,
                $_POST['user_code'] ?? null,
                $_POST['user_name'] ?? '',
                $_POST['user_lastname'] ?? '',
                $_POST['user_id'] ?? '',
                $_POST['user_email'] ?? '',
                $_POST['user_pass'] ?? '',
                $_POST['user_state'] ?? ''
            );

            $userUpdate->update_user();
            header("Location: ?c=Users&a=userRead");
            exit;
        }
    }
    // Controlador Eliminar Usuario
     
    public function userDelete()
    {
        if (!$this->isAdmin()) {
            header("Location: ?c=Dashboard");
            exit;
        }

        if (isset($_GET['idUser'])) {
            $user = new User();
            $user->delete_user($_GET['idUser']);
        }

        header("Location: ?c=Users&a=userRead");
        exit;
    }
}
?>
