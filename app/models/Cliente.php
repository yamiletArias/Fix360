<?php
require_once "../models/Conexion.php";

class Cliente extends Conexion {

  private $pdo;

  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }
 
  public function registerClientePersona($params = []): int {
    $numRows = 0;
    try {
      $query = "CALL spRegisterClientePersona(?,?,?,?,?,?,?,?,?,?)";
      $stmt = $this->pdo->prepare($query);
      $stmt->execute(array(
        $params["nombres"],
        $params["apellidos"],
        $params["tipodoc"],
        $params["numdoc"],
        $params["numruc"],
        $params["direccion"],
        $params["correo"],
        $params["telprincipal"],
        $params["telalternativo"],
        $params["idcontactabilidad"]
      ));

      $numRows = $stmt->rowCount();

    } catch (PDOException $e) {
      error_log("Error DB: " . $e->getMessage());
      return $numRows;
    } 
    return $numRows;
  }

  public function registerClienteEmpresa($params = []): int {
    $numRows = 0;
    try {
      $query = "CALL spRegisterClienteEmpresa(?,?,?,?,?,?)";
      $stmt = $this->pdo->prepare($query);
      $stmt->execute(array(
        $params["ruc"],
        $params["nomcomercial"],
        $params["razonsocial"],
        $params["telefono"],
        $params["correo"],
        $params["idcontactabilidad"]
      ));

      $numRows = $stmt->rowCount();

    } catch (PDOException $e) {
      error_log("Error DB: " . $e->getMessage());
      return $numRows;
    } 
    return $numRows;
  }

  public function getAllClientesPersona(): array{
    $result = [];

    try {
      $sql = "SELECT * FROM vwClientesPersona ORDER BY nombres";

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      throw new Exception($e->getMessage());
    }
    return $result;
  }

  public function getAllClientesEmpresa(): array{

    $result = [];

    try {
      $sql = "SELECT * FROM vwClientesEmpresa ORDER BY nomcomercial";

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      throw new Exception($e->getMessage());
    }
    return $result;
  }

  
}