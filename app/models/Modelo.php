<?php

require_once "../models/Conexion.php";

class Modelo extends Conexion {
  private $conexion;

  public function __construct()  {
    $this->conexion = Conexion::getConexion();
  }

  public function GetAllModelosByTipoMarca($params = []): array{
    $result = [];
    try {
      $sql = "CALL spGetModelosByTipoMarca(?,?) ";

      $stmt = $this->conexion->prepare($sql);
      $stmt->execute(
        array(
          $params["idtipov"],
          $params["idmarca"]
        )
        );
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      throw new Exception($e->getMessage());
    }
    return $result;
  }

  public function registerModelo(array $params): int {
    try {
        // Llamamos al SP que devuelve SELECT LAST_INSERT_ID()
        $sql = "CALL spRegisterModelo(?, ?, ?)";
        $cmd = $this->conexion->prepare($sql);
        $cmd->execute([
            $params['idtipov'],
            $params['idmarca'],
            $params['modelo']     // el nombre del modelo, no 'idvehiculo'
        ]);
        // Leemos la fila con ['idmodelo']
        $row = $cmd->fetch(PDO::FETCH_ASSOC);
        return isset($row['idmodelo'])
             ? (int)$row['idmodelo']
             : 0;
    } catch (\PDOException $e) {
        error_log("Error DB en registerModelo: " . $e->getMessage());
        return 0;
    }
}


  
}