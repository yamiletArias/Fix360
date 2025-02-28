<?php

require_once "../models/Conexion.php";

class Empresa extends Conexion {

  protected $pdo;

  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Retorna todas las empresas registradas
   * @return array
   */
  public function getAll() {
    try {
      $query = "CALL spListEmpresas()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Agrega una nueva empresa
   * @param array $params
   * @return array
   */
  public function add($params = []) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spRegisterEmpresa(?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["razonsocial"],
        $params["telefono"],
        $params["correo"],
        $params["ruc"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Empresa registrada correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Obtiene una empresa por RUC
   * @param string $ruc
   * @return array
   */
  public function find($ruc) {
    try {
      $query = "CALL spGetEmpresaByRuc(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$ruc]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualiza los datos de una empresa
   * @param array $params
   * @return array
   */
  public function update($params = []) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateEmpresa(?, ?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idempresa"],
        $params["razonsocial"],
        $params["telefono"],
        $params["correo"],
        $params["ruc"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Empresa actualizada correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Elimina una empresa por ID
   * @param int $idempresa
   * @return array
   */
  public function delete($idempresa) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteEmpresa(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idempresa]);
      $resultado["status"] = true;
      $resultado["message"] = "Empresa eliminada correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
