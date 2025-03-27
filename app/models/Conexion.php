<?php

require_once "../helpers/helper.php";
require_once __DIR__ . "/../config/Server.php";

class Conexion extends Helper {

    // Cambiar a public static para poder acceder a ella desde otras clases
    public static function getConexion() {
        try {
            // Crear la conexión usando los parámetros del archivo de configuración
            $pdo = new PDO(SGBD, USER, PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (Exception $e) {
            // En caso de error, mostrar el mensaje de error
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>
