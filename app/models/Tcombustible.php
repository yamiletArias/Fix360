<?php
require_once "../models/Conexion.php";

class Tcombustible extends Conexion {
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::getConexion();
    }

    public function getAll(): array{
        $result = [];

        try {
         $sql = "SELECT * FROM vwtcombustible ORDER BY tcombustible ASC";
         $stmt = $this->conexion->prepare($sql);
         $stmt->execute();
         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    public function registerTcombustible($params = []):int {
        $numRows = 0;
        try {
            $sql = "CALL spRegisterTcombustible(?)";
             $cmd = $this->conexion->prepare($sql);
             $cmd->execute([
                $params['tcombustible']
             ]);
             $row = $cmd->fetch(PDO::FETCH_ASSOC);
             return isset($row['idtcombustible']) ? (int)$row['idtcombustible'] : 0;
        } catch (PDOException $e) {
        error_log("Error DB: " . $e->getMessage());
        return 0;
    }

    }
}