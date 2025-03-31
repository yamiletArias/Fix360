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

  
}