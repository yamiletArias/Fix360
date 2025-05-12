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

  public function update($params = []): int{
    $numRows = 0;
    try {
      $query = "CALL spUpdateObservacion(?,?,?,?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idobservacion"],
        $params["idcomponente"],
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

  public function delete($idobservacion):array{
    $result = [];
    try {
      $query = "CALL spDeleteObservacion(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute(array($idobservacion));
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
    return $result;
  }

  // En app/models/Observacion.php, dentro de la clase Observacion:
public function find(int $idobservacion): array
{
    try {
        // Solo cogemos la ruta de la foto; si necesitas más columnas, añádelas
        $sql = "SELECT foto FROM observaciones WHERE idobservacion = ?";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$idobservacion]);
        $row = $cmd->fetch(PDO::FETCH_ASSOC);
        return $row ?: [];
    } catch (PDOException $e) {
        error_log("Error en find(): " . $e->getMessage());
        return [];
    }
}

  

  
}