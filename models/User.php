<?php

namespace App\Models;

class User
{
    /* =========================
     * 1. ATRIBUTOS
     * ========================= */
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

    /* =========================
     * 2. CONSTRUCTOR PRINCIPAL
     * ========================= */
    public function __construct()
    {
        try {
            $this->dbh = DataBase::connection();
            $args = func_get_args();
            $num  = func_num_args();

            if (method_exists($this, $method = 'construct' . $num)) {
                call_user_func_array([$this, $method], $args);
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /* =========================
     * 3. SOBRECARGA DE CONSTRUCTORES
     * ========================= */
    public function construct0() {}

    public function construct2($userEmail, $userPass)
    {
        $this->userEmail = $userEmail;
        $this->userPass  = $userPass;
    }

    public function construct8(
        $rolCode,
        $userCode,
        $userName,
        $userLastname,
        $userId,
        $userEmail,
        $userPass,
        $userState
    ) {
        $this->rolCode      = $rolCode;
        $this->userCode     = $userCode;
        $this->userName     = $userName;
        $this->userLastname = $userLastname;
        $this->userId       = $userId;
        $this->userEmail    = $userEmail;
        $this->userPass     = $userPass;
        $this->userState    = $userState;
    }

    public function construct9(
        $rolCode,
        $rolName,
        $userCode,
        $userName,
        $userLastname,
        $userId,
        $userEmail,
        $userPass,
        $userState
    ) {
        unset($this->dbh);
        $this->rolCode      = $rolCode;
        $this->rolName      = $rolName;
        $this->userCode     = $userCode;
        $this->userName     = $userName;
        $this->userLastname = $userLastname;
        $this->userId       = $userId;
        $this->userEmail    = $userEmail;
        $this->userPass     = $userPass;
        $this->userState    = $userState;
    }

    /* =========================
     * 4. GETTERS Y SETTERS
     * ========================= */
    public function setRolCode($rolCode)      { $this->rolCode = $rolCode; }
    public function getRolCode()              { return $this->rolCode; }

    public function setRolName($rolName)      { $this->rolName = $rolName; }
    public function getRolName()              { return $this->rolName; }

    public function setUserCode($userCode)    { $this->userCode = $userCode; }
    public function getUserCode()             { return $this->userCode; }

    public function setUserName($userName)    { $this->userName = $userName; }
    public function getUserName()             { return $this->userName; }

    public function setUserLastName($userLastname) { $this->userLastname = $userLastname; }
    public function getUserLastName()               { return $this->userLastname; }

    public function setUserId($userId)        { $this->userId = $userId; }
    public function getUserId()               { return $this->userId; }

    public function setUserEmail($userEmail)  { $this->userEmail = $userEmail; }
    public function getUserEmail()            { return $this->userEmail; }

    public function setUserPass($userPass)    { $this->userPass = $userPass; }
    public function getUserPass()             { return $this->userPass; }

    public function setUserState($userState)  { $this->userState = $userState; }
    public function getUserState()            { return $this->userState; }

    /* =========================
     * 5. MÉTODOS DE NEGOCIO
     * ========================= */

    /* LOGIN */
    public function login()
    {
        try {
            $sql = "
                SELECT r.rol_code, r.rol_name,
                       user_code, user_name, user_lastname,
                       user_id, user_email, user_pass, user_state
                FROM ROLES r
                INNER JOIN USERS u ON r.rol_code = u.rol_code
                WHERE user_email = :userEmail AND user_pass = :userPass
            ";

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('userEmail', $this->getUserEmail());
            $stmt->bindValue('userPass', sha1($this->getUserPass()));
            $stmt->execute();

            $u = $stmt->fetch();

            return $u ? new User(
                $u['rol_code'], $u['rol_name'], $u['user_code'],
                $u['user_name'], $u['user_lastname'], $u['user_id'],
                $u['user_email'], $u['user_pass'], $u['user_state']
            ) : false;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /* ROLES */
    public function createRol()
    {
        $sql = "INSERT INTO ROLES VALUES (:rolCode,:rolName)";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue('rolCode', $this->getRolCode());
        $stmt->bindValue('rolName', $this->getRolName());
        $stmt->execute();
    }

    public function readRoles()
    {
        $roles = [];
        foreach ($this->dbh->query("SELECT * FROM ROLES")->fetchAll() as $r) {
            $rol = new User();
            $rol->setRolCode($r['rol_code']);
            $rol->setRolName($r['rol_name']);
            $roles[] = $rol;
        }
        return $roles;
    }

    public function getRolByCode($rolCode)
    {
        $stmt = $this->dbh->prepare("SELECT * FROM ROLES WHERE rol_code=:rolCode");
        $stmt->bindValue('rolCode', $rolCode);
        $stmt->execute();
        $r = $stmt->fetch();

        return new User($r['rol_code'], $r['rol_name'], null, null, null, null, null, null, null);
    }

    public function updateRol()
    {
        $stmt = $this->dbh->prepare(
            "UPDATE ROLES SET rol_name=:rolName WHERE rol_code=:rolCode"
        );
        $stmt->bindValue('rolCode', $this->getRolCode());
        $stmt->bindValue('rolName', $this->getRolName());
        $stmt->execute();
    }

    public function deleteRol($rolCode)
    {
        $stmt = $this->dbh->prepare("DELETE FROM ROLES WHERE rol_code=:rolCode");
        $stmt->bindValue('rolCode', $rolCode);
        $stmt->execute();
    }

    /* USUARIOS */
    public function createUser()
    {
        $sql = "
            INSERT INTO USERS VALUES (
                :rolCode,:userCode,:userName,:userLastname,
                :userId,:userEmail,:userPass,:userState
            )
        ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue('rolCode', $this->getRolCode());
        $stmt->bindValue('userCode', $this->getUserCode());
        $stmt->bindValue('userName', $this->getUserName());
        $stmt->bindValue('userLastname', $this->getUserLastName());
        $stmt->bindValue('userId', $this->getUserId());
        $stmt->bindValue('userEmail', $this->getUserEmail());
        $stmt->bindValue('userPass', sha1($this->getUserPass()));
        $stmt->bindValue('userState', $this->getUserState());
        $stmt->execute();
    }

    public function readUsers()
    {
        $users = [];
        $sql = "
            SELECT r.rol_code, r.rol_name,
                   user_code, user_name, user_lastname,
                   user_id, user_email, user_pass, user_state
            FROM ROLES r
            INNER JOIN USERS u ON r.rol_code = u.rol_code
        ";

        foreach ($this->dbh->query($sql)->fetchAll() as $u) {
            $users[] = new User(
                $u['rol_code'], $u['rol_name'], $u['user_code'],
                $u['user_name'], $u['user_lastname'], $u['user_id'],
                $u['user_email'], $u['user_pass'], $u['user_state']
            );
        }
        return $users;
    }

    public function getUserByCode($userCode)
    {
        $stmt = $this->dbh->prepare("
            SELECT r.rol_code, r.rol_name,
                   user_code, user_name, user_lastname,
                   user_id, user_email, user_pass, user_state
            FROM ROLES r
            INNER JOIN USERS u ON r.rol_code = u.rol_code
            WHERE user_code=:userCode
        ");
        $stmt->bindValue('userCode', $userCode);
        $stmt->execute();
        $u = $stmt->fetch();

        return new User(
            $u['rol_code'], $u['rol_name'], $u['user_code'],
            $u['user_name'], $u['user_lastname'], $u['user_id'],
            $u['user_email'], $u['user_pass'], $u['user_state']
        );
    }

    public function updateUser()
    {
        $sql = "
            UPDATE USERS SET
                rol_code=:rolCode,
                user_name=:userName,
                user_lastname=:userLastname,
                user_id=:userId,
                user_email=:userEmail,
                user_pass=:userPass,
                user_state=:userState
            WHERE user_code=:userCode
        ";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue('rolCode', $this->getRolCode());
        $stmt->bindValue('userCode', $this->getUserCode());
        $stmt->bindValue('userName', $this->getUserName());
        $stmt->bindValue('userLastname', $this->getUserLastName());
        $stmt->bindValue('userId', $this->getUserId());
        $stmt->bindValue('userEmail', $this->getUserEmail());
        $stmt->bindValue('userPass', sha1($this->getUserPass()));
        $stmt->bindValue('userState', $this->getUserState());
        $stmt->execute();
    }

    public function deleteUser($userCode)
    {
        $stmt = $this->dbh->prepare("DELETE FROM USERS WHERE user_code=:userCode");
        $stmt->bindValue('userCode', $userCode);
        $stmt->execute();
    }
}
