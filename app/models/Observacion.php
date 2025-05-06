<?php

require_once __DIR__ . '/Conexion.php';

class Observacion extends Conexion{
  protected $pdo;

  public function __construct(){
    $this->pdo = parent::getConexion();
  }

  public function getObservacionByOrden($idorden):array{
    $result = [];
    try {
      $query = "CALL spGetObservacionByOrden(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute(array($idorden));
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
    return $result;
  }

  public function add($params = []): int{
    $numRows = 0;
    try {
      $query = "CALL spRegisterObservacion(?,?,?,?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idcomponente"],
        $params["idorden"],
        $params["estado"],
        $params["foto"]
      ]);
      $numRows = $cmd->rowCount();
    } catch (PDOException $e) {
      error_log("Error DB: " . $e->getMessage());
      return $numRows;
    }
    return $numRows;
  }

  

  
}