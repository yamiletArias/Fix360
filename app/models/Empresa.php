<?php

require_once "../models/Conexion.php";

/**
 * Clase EmpresaModel
 * Maneja las operaciones CRUD de la tabla 'empresas'
 */
class Empresa extends Conexion
{

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }


  /**
   * Obtener todas las empresas
   * @return array Lista de empresas
   */
  public function getAll()
  {
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
   * Registrar una nueva empresa
   * @param array $params Datos de la empresa
   * @return array Resultado de la operación
   */
  public function add($params = [])
  {
    $resultado = ["status" => false, "message" => "", "idempresa" => null];
    try {
      $sql = "INSERT INTO empresas (nomcomercial, razonsocial, telefono, correo, ruc)
                 VALUES (?, ?, ?, ?, ?)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([
        $params["nomcomercial"],
        $params["razonsocial"],
        $params["telefono"],
        $params["correo"],
        $params["ruc"]
      ]);

      $id = $this->pdo->lastInsertId();
      if ($id) {
        $resultado["status"] = true;
        $resultado["message"] = "Empresa registrada correctamente";
        $resultado["idempresa"] = $id;
      } else {
        $resultado["message"] = "No se obtuvo ID tras insertar la empresa.";
      }
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    }
    return $resultado;
  }
  /*   public function add($params = []) {
      $resultado = ["status" => false, "message" => ""];
      try {
        $query = "CALL spRegisterEmpresa(?, ?, ?, ?, ?)";
        $cmd = $this->pdo->prepare($query);
        $cmd->execute([
          $params["nomcomercial"],
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
    } */

  /**
   * Buscar empresa por RUC
   * @param string $ruc Número de RUC
   * @return array Datos de la empresa encontrada
   */
  public function find($id)
  {
    try {
      $query = "CALL spGetEmpresaById(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$id]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar una empresa
   * @param array $params Datos de la empresa
   * @return array Resultado de la operación
   */
  public function update($params = [])
  {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateEmpresa( ?, ?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idempresa"],
        $params["nomcomercial"],
        $params["razonsocial"],
        $params["telefono"],
        $params["correo"]
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
   * Eliminar una empresa
   * @param int $idempresa ID de la empresa a eliminar
   * @return array Resultado de la operación
   */
  public function delete($idempresa)
  {
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

  public function GetById($idempresa): array
  {
    $result = [];

    try {
      $sql = "CALL spGetEmpresaById(?)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute(array($idempresa));
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }

    return $result;

  }
}
