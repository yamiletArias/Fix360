<?php
require_once "../models/Conexion.php";

class Tipov extends Conexion {

  private $conexion;

  public function __construct()
  {
    $this->conexion = Conexion::getConexion();
  }

  public function getAll(): array{
    $result = [];

    try {
      $sql = "CALL spGetAllTipoVehiculo()";
      $stmt = $this->conexion->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      throw new Exception($e->getMessage());
    }

    return $result;
  }



}