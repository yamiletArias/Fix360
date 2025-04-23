<?php

require_once "../models/Conexion.php";

class Servicio extends Conexion
{
  protected $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

  public function getServicioBySubcategoria($idsubcategoria): array
  {
    $result = [];
    try {
      $query = "CALL spGetServicioBySubcategoria(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute(
        array($idsubcategoria)
      );
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
    return $result;
  }

  public function registerServicio($params =[]): int{
    $numRows = 0;
    try {
      $query = "CALL spRegisterServicio(?,?)";
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([
        $params["idsubcategoria"],
        $params["servicio"]
      ]);
      $numRows = $stmt->rowCount();
    } catch (PDOException $e) {
      error_log("Error DB: " . $e->getMessage());
      return $numRows;
    }
    return $numRows;
  }

  
}
