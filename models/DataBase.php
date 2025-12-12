<?php
namespace App\Models;

use PDO;

class DataBase{

    public static function connection(){

        // Host del servidor MySQL de Azure
        $hostname = "serverdatabase2.mysql.database.azure.com";
        $port     = "3306";


        $database = "database_php";

        $username = "Adminda2@serverdatabase2";


        $password = getenv('DB_PASSWORD');


        $options = [
            PDO::MYSQL_ATTR_SSL_CA => 'assets/database/DigiCertGlobalRootG2.crt.pem',
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ];

        // Crear conexión
        $pdo = new PDO(
            "mysql:host=$hostname;port=$port;dbname=$database;charset=utf8",
            $username,
            $password,
            $options
        );

        // Manejo de errores
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}

