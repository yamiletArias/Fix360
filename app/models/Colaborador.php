<?php

require_once "Conexion.php";

class Colaborador extends Conexion {

    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    public function login($namuser) {
        try {
            $stmt = $this->pdo->prepare("CALL spu_colaboradores_login(:namuser)");
            $stmt->bindParam(":namuser", $namuser, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["esCorrecto" => false, "mensaje" => "Error en login: " . $e->getMessage()];
        }
    }    
}
