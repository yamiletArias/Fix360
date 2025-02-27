<?php

require_once "Conexion.php";

class Colaborador extends Conexion {

    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    public function login($params = []): array {
        try {
            $cmd = $this->pdo->prepare("CALL spu_colaboradores_login(?);");
            $cmd->execute([$params['namuser']]);
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error Login: " . $e->getMessage());
            return [];
        }
    }
}
