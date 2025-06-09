<?php

require_once "../models/Conexion.php";

/**
 * Clase ProveedoresModel
 * Maneja las operaciones CRUD de la tabla 'proveedores'
 */
class Proveedores extends Conexion
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
   * Obtener todos los proveedores con la información de la empresa asociada
   * @return array Lista de proveedores con sus empresas
   */
  public function getAll()
  {
    try {
      $query = "CALL spListProveedores()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Registrar un nuevo proveedor vinculándolo a una empresa
   * @param int $idempresa ID de la empresa
   * @return array Resultado de la operación
   */
  public function add(int $idempresa): array
  {
    $resultado = ["status" => false, "message" => "", "idproveedor" => null];
    try {
      $sql = "INSERT INTO proveedores (idempresa) VALUES (?)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$idempresa]);
      $idprov = (int) $this->pdo->lastInsertId();
      $resultado = [
        "status" => true,
        "message" => "Proveedor registrado correctamente",
        "idproveedor" => $idprov
      ];
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    }
    return $resultado;
  }

  /**
   * Registrar un nuevo proveedor
   * @param int $idempresa ID de la empresa asociada al proveedor
   * @return array Resultado de la operación
   */
  /*   public function add($idempresa) {
      $resultado = ["status" => false, "message" => ""];
      try {
        $query = "CALL spRegisterProveedor(?)";
        $cmd = $this->pdo->prepare($query);
        $cmd->execute([$idempresa]);
        $resultado["status"] = true;
        $resultado["message"] = "Proveedor registrado correctamente";
      } catch (Exception $e) {
        $resultado["message"] = $e->getMessage();
      } finally {
        return $resultado;
      }
    } */

  /**
   * Buscar un proveedor por su ID
   * @param int $idproveedor ID del proveedor
   * @return array Datos del proveedor encontrado
   */
  public function find($idproveedor)
  {
    try {
      $query = "CALL spGetProveedorById(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idproveedor]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar un proveedor
   * @param int $idproveedor ID del proveedor a actualizar
   * @param int $idempresa Nuevo ID de la empresa asociada
   * @return array Resultado de la operación
   */
  public function update($idproveedor, $idempresa)
  {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateProveedor(?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idproveedor, $idempresa]);
      $resultado["status"] = true;
      $resultado["message"] = "Proveedor actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Eliminar un proveedor
   * @param int $idproveedor ID del proveedor a eliminar
   * @return array Resultado de la operación
   */
  public function delete($idproveedor)
  {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteProveedor(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idproveedor]);
      $resultado["status"] = true;
      $resultado["message"] = "Proveedor eliminado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
