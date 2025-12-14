<?php

namespace App\Models;

class User
{
    // 1ra Parte: Atributos
    private $dbh;

    private $rolCode;
    private $rolName;

    private $userCode;
    private $userName;
    private $userLastname;
    private $userId;
    private $userEmail;
    private $userPass;
    private $userState;

    // 2da Parte: Sobrecarga Constructores
    public function __construct()
    {
        try {
            $this->dbh = DataBase::connection();
            $args = func_get_args();
            $numArgs = func_num_args();

            if (method_exists($this, $method = 'construct' . $numArgs)) {
                call_user_func_array([$this, $method], $args);
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // Constructor: 0 parámetros
    public function construct0(): void
    {
    }

    // Constructor: 1 parámetro (DTO array para evitar 8/9 parámetros)
    public function construct1(array $data): void
    {
        // Si llega rolName, no necesitamos conexión en este objeto (como tu construct9 original)
        if (array_key_exists('rolName', $data)) {
            unset($this->dbh);
        }

        // Roles
        if (array_key_exists('rolCode', $data)) {
            $this->rolCode = $data['rolCode'];
        }
        if (array_key_exists('rolName', $data)) {
            $this->rolName = $data['rolName'];
        }

        // Usuario
        if (array_key_exists('userCode', $data)) {
            $this->userCode = $data['userCode'];
        }
        if (array_key_exists('userName', $data)) {
            $this->userName = $data['userName'];
        }
        if (array_key_exists('userLastname', $data)) {
            $this->userLastname = $data['userLastname'];
        }
        if (array_key_exists('userId', $data)) {
            $this->userId = $data['userId'];
        }
        if (array_key_exists('userEmail', $data)) {
            $this->userEmail = $data['userEmail'];
        }
        if (array_key_exists('userPass', $data)) {
            $this->userPass = $data['userPass'];
        }
        if (array_key_exists('userState', $data)) {
            $this->userState = $data['userState'];
        }
    }

    // Constructor: 2 parámetros (login)
    public function construct2($userEmail, $userPass): void
    {
        $this->userEmail = $userEmail;
        $this->userPass  = $userPass;
    }

    // 3ra Parte: Setter y Getters
    public function setRolCode($rolCode): void { $this->rolCode = $rolCode; }
    public function getRolCode() { return $this->rolCode; }

    public function setRolName($rolName): void { $this->rolName = $rolName; }
    public function getRolName() { return $this->rolName; }

    public function setUserCode($userCode): void { $this->userCode = $userCode; }
    public function getUserCode() { return $this->userCode; }

    public function setUserName($userName): void { $this->userName = $userName; }
    public function getUserName() { return $this->userName; }

    public function setUserLastName($userLastname): void { $this->userLastname = $userLastname; }
    public function getUserLastName() { return $this->userLastname; }

    public function setUserId($userId): void { $this->userId = $userId; }
    public function getUserId() { return $this->userId; }

    public function setUserEmail($userEmail): void { $this->userEmail = $userEmail; }
    public function getUserEmail() { return $this->userEmail; }

    public function setUserPass($userPass): void { $this->userPass = $userPass; }
    public function getUserPass() { return $this->userPass; }

    public function setUserState($userState): void { $this->userState = $userState; }
    public function getUserState() { return $this->userState; }

    // 4ta Parte: Persistencia a la Base de Datos

    // RF01_CU01 - Iniciar Sesión
    public function login()
    {
        try {
            $sql = 'SELECT
                        r.rol_code,
                        r.rol_name,
                        user_code,
                        user_name,
                        user_lastname,
                        user_id,
                        user_email,
                        user_pass,
                        user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                    on r.rol_code = u.rol_code
                    WHERE user_email = :userEmail AND user_pass = :userPass';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('userEmail', $this->getUserEmail());
            $stmt->bindValue('userPass', sha1($this->getUserPass()));
            $stmt->execute();

            $userDb = $stmt->fetch();

            return $userDb ? new User([
                'rolCode'      => $userDb['rol_code'],
                'rolName'      => $userDb['rol_name'],
                'userCode'     => $userDb['user_code'],
                'userName'     => $userDb['user_name'],
                'userLastname' => $userDb['user_lastname'],
                'userId'       => $userDb['user_id'],
                'userEmail'    => $userDb['user_email'],
                'userPass'     => $userDb['user_pass'],
                'userState'    => $userDb['user_state'],
            ]) : false;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF03_CU03 - Registrar Rol
    public function createRol(): void
    {
        try {
            $sql = 'INSERT INTO ROLES VALUES (:rolCode,:rolName)';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('rolName', $this->getRolName());
            $stmt->execute();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF04_CU04 - Consultar Roles
    public function readRoles(): array
    {
        try {
            $rolList = [];
            $sql = 'SELECT * FROM ROLES';
            $stmt = $this->dbh->query($sql);

            foreach ($stmt->fetchAll() as $rol) {
                $rolObj = new User();
                $rolObj->setRolCode($rol['rol_code']);
                $rolObj->setRolName($rol['rol_name']);
                $rolList[] = $rolObj;
            }
            return $rolList;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF05_CU05 - Obtener el Rol por el código
    public function getRolByCode($rolCode)
    {
        try {
            $sql  = 'SELECT * FROM ROLES WHERE rol_code=:rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $rolCode);
            $stmt->execute();

            $rolDb = $stmt->fetch();
            $rol = new User();
            $rol->setRolCode($rolDb['rol_code']);
            $rol->setRolName($rolDb['rol_name']);

            return $rol;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF06_CU06 - Actualizar Rol
    public function updateRol(): void
    {
        try {
            $sql = 'UPDATE ROLES SET
                        rol_code = :rolCode,
                        rol_name = :rolName
                    WHERE rol_code = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('rolName', $this->getRolName());
            $stmt->execute();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF07_CU07 - Eliminar Rol
    public function deleteRol($rolCode): void
    {
        try {
            $sql  = 'DELETE FROM ROLES WHERE rol_code = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $rolCode);
            $stmt->execute();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF08_CU08 - Registrar Usuario
    public function createUser(): void
    {
        try {
            $sql = 'INSERT INTO USERS VALUES (
                        :rolCode,
                        :userCode,
                        :userName,
                        :userLastName,
                        :userId,
                        :userEmail,
                        :userPass,
                        :userState
                    )';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('userCode', $this->getUserCode());
            $stmt->bindValue('userName', $this->getUserName());
            $stmt->bindValue('userLastName', $this->getUserLastName());
            $stmt->bindValue('userId', $this->getUserId());
            $stmt->bindValue('userEmail', $this->getUserEmail());
            $stmt->bindValue('userPass', sha1($this->getUserPass()));
            $stmt->bindValue('userState', $this->getUserState());
            $stmt->execute();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF09_CU09 - Consultar Usuarios
    public function readUsers(): array
    {
        try {
            $userList = [];
            $sql = 'SELECT
                        r.rol_code,
                        r.rol_name,
                        user_code,
                        user_name,
                        user_lastname,
                        user_id,
                        user_email,
                        user_pass,
                        user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                    on r.rol_code = u.rol_code';

            $stmt = $this->dbh->query($sql);

            foreach ($stmt->fetchAll() as $user) {
                $userList[] = new User([
                    'rolCode'      => $user['rol_code'],
                    'rolName'      => $user['rol_name'],
                    'userCode'     => $user['user_code'],
                    'userName'     => $user['user_name'],
                    'userLastname' => $user['user_lastname'],
                    'userId'       => $user['user_id'],
                    'userEmail'    => $user['user_email'],
                    'userPass'     => $user['user_pass'],
                    'userState'    => $user['user_state'],
                ]);
            }

            return $userList;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF10_CU10 - Obtener el Usuario por el código
    public function getUserByCode($userCode)
    {
        try {
            $sql = 'SELECT
                        r.rol_code,
                        r.rol_name,
                        user_code,
                        user_name,
                        user_lastname,
                        user_id,
                        user_email,
                        user_pass,
                        user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                    on r.rol_code = u.rol_code
                    WHERE user_code=:userCode';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('userCode', $userCode);
            $stmt->execute();

            $userDb = $stmt->fetch();

            return new User([
                'rolCode'      => $userDb['rol_code'],
                'rolName'      => $userDb['rol_name'],
                'userCode'     => $userDb['user_code'],
                'userName'     => $userDb['user_name'],
                'userLastname' => $userDb['user_lastname'],
                'userId'       => $userDb['user_id'],
                'userEmail'    => $userDb['user_email'],
                'userPass'     => $userDb['user_pass'],
                'userState'    => $userDb['user_state'],
            ]);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF11_CU11 - Actualizar usuario
    public function updateUser(): void
    {
        try {
            $sql = 'UPDATE USERS SET
                        rol_code = :rolCode,
                        user_code = :userCode,
                        user_name = :userName,
                        user_lastname = :userLastName,
                        user_id = :userId,
                        user_email = :userEmail,
                        user_pass = :userPass,
                        user_state = :userState
                    WHERE user_code = :userCode';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('userCode', $this->getUserCode());
            $stmt->bindValue('userName', $this->getUserName());
            $stmt->bindValue('userLastName', $this->getUserLastName());
            $stmt->bindValue('userId', $this->getUserId());
            $stmt->bindValue('userEmail', $this->getUserEmail());
            $stmt->bindValue('userPass', sha1($this->getUserPass()));
            $stmt->bindValue('userState', $this->getUserState());
            $stmt->execute();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // RF12_CU12 - Eliminar Usuario
    public function deleteUser($userCode): void
    {
        try {
            $sql  = 'DELETE FROM USERS WHERE user_code = :userCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('userCode', $userCode);
            $stmt->execute();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
