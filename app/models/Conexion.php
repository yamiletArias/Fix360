<?php

require_once "../helpers/helper.php";
require_once __DIR__ . "/../config/Server.php";

class Conexion extends Helper{

    protected static function getConexion() {
        try {
            $pdo = new PDO(SGBD, USER, PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (Exception $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    }

