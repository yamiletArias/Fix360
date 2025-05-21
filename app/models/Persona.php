<?php

require_once "../models/Conexion.php";

class Persona extends Conexion {

  protected $pdo;

  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  public function getAll() {
    try {
      $query = "CALL spListPersonas()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  public function add($params = []) {
    $resultado = [
      "status"  => false,
      "message" => ""
    ];
    try {
      $query = "CALL spRegisterPersona(?, ?, ?, ?, ?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["nombres"],
        $params["apellidos"],
        $params["tipodoc"],
        $params["numdoc"],
        $params["numruc"],
        $params["direccion"],
        $params["correo"],
        $params["telprincipal"],
        $params["telalternativo"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "El proceso finalizó correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  public function find($params = []) {
    try {
      $query = "CALL spGetPersonaByNumdoc(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["numdoc"]
      ]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  public function update($params = []) {
    $resultado = [
      "status"  => false,
      "message" => ""
    ];
    try {
      $query = "CALL spUpdatePersona(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idpersona"],
        $params["nombres"],
        $params["apellidos"],
        $params["tipodoc"],
        $params["numdoc"],
        $params["numruc"],
        $params["direccion"],
        $params["correo"],
        $params["telprincipal"],
        $params["telalternativo"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Registro actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  public function delete($params = []) {
    $resultado = [
      "status"  => false,
      "message" => ""
    ];
    try {
      $query = "CALL spDeletePersona(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idpersona"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Registro eliminado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  public function GetById($idpersona):array {
    $result = [];

    try {
      $sql = "CALL spGetPersonaById(?)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute(array($idpersona));
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }

    return $result;

  }
}
