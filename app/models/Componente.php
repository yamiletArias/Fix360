<?php
require_once "../models/Conexion.php";

class Componente extends Conexion{

  private $pdo;
  public function __construct(){
    $this->pdo = parent::getConexion();
  }

  public function getAll(): array{
    $result = [];
    try {
      $sql = "SELECT * FROM vwComponentes";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      throw new Exception($e->getMessage());
    }
    return $result;
  }

  public function add($params= []): array{
    $response = ['idcomponente' => 0, 'componente' => ''];
    try {
      $query = "CALL spRegisterComponente(?)";
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([
        $params["componente"]
      ]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row && isset($row['idcomponente'])) {
        $response['idcomponente'] = (int)$row['idcomponente'];
        $response['componente']   = $params["componente"];
      }
    } catch (PDOException $e) {
      error_log("Error DB: " . $e->getMessage());
      return $response;
    }
    return $response;
  }
}