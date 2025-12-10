<?php
namespace App\Models;

use PDO;

class DataBase{

    public static function connection(){

        // Host del servidor MySQL de Azure
        $hostname = "serverdatabase2.mysql.database.azure.com";
        $port     = "3306";

        // Nombre exacto de tu base
        $database = "database_php";

        // Usuario administrador con el sufijo del servidor
        $username = "Adminda2@serverdatabase2";

        // Contraseña que configuraste o restablezcas en Azure
        $password = "A4min123.";

        // Certificado SSL obligatorio en Azure
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
?>
