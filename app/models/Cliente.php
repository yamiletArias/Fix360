<?php
require_once "../models/Conexion.php";

class Cliente extends Conexion
{

  private $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

  public function registerClientePersona($params = []): array
  {
    try {
      // Preparo el SP
      $query = "CALL spRegisterClientePersona(?,?,?,?,?,?,?,?,?,?)";
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([
        $params["nombres"],
        $params["apellidos"],
        $params["tipodoc"],
        $params["numdoc"],
        $params["numruc"] ?? null,
        $params["direccion"] ?? null,
        $params["correo"] ?? null,
        $params["telprincipal"] ?? null,
        $params["telalternativo"] ?? null,
        $params["idcontactabilidad"]
      ]);

      // rowCount() del CALL (normalmente 0 en un SP, pero lo devolvemos igual)
      $rowsAffected = $stmt->rowCount();

      // Este lastInsertId() va a ser el idCliente recién insertado dentro del SP
      $idcliente = (int) $this->pdo->lastInsertId();

      return [
        "rows" => $rowsAffected,
        "idcliente" => $idcliente
      ];
    } catch (PDOException $e) {
      error_log("Error DB registerClientePersona: " . $e->getMessage());
      return [
        "rows" => 0,
        "idcliente" => 0
      ];
    }
  }

  public function registerClienteEmpresa($params = []): array
  {
    try {
      $query = "CALL spRegisterClienteEmpresa(?,?,?,?,?,?)";
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([
        $params["ruc"],
        $params["nomcomercial"],
        $params["razonsocial"],
        $params["telefono"] ?? null,
        $params["correo"] ?? null,
        $params["idcontactabilidad"]
      ]);

      $rowsAffected = $stmt->rowCount();
      $idcliente = (int) $this->pdo->lastInsertId();

      return [
        "rows" => $rowsAffected,
        "idcliente" => $idcliente
      ];
    } catch (PDOException $e) {
      error_log("Error DB registerClienteEmpresa: " . $e->getMessage());
      return [
        "rows" => 0,
        "idcliente" => 0
      ];
    }
  }

  public function getAllClientesPersona(): array
  {
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

  public function getAllClientesEmpresa(): array
  {

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

  public function getClienteById($idcliente): array
  {

    $result = [];

    try {
      $sql = "CALL spGetClienteById(?)";
      $cmd = $this->pdo->prepare($sql);
      $cmd->execute(
        array($idcliente)
      );
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }

    return $result;
  }

  public function getPersonaById($idpersona): array
  {

    $result = [];

    try {
      $sql = " CALL spGetPersonaById(?)";
      $cmd = $this->pdo->prepare($sql);
      $cmd->execute(
        array($idpersona)
      );
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
    return $result;
  }

  public function getEmpresaById($idempresa): array
  {

    $result = [];

    try {
      $sql = " CALL spGetEmpresaById(?)";
      $cmd = $this->pdo->prepare($sql);
      $cmd->execute(
        array($idempresa)
      );
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
    return $result;
  }






}