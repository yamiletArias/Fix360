<?php

require_once "../models/Conexion.php";

/**
 * Clase Kardex
 * Maneja las operaciones CRUD de la tabla 'kardex'
 */
class Kardex extends Conexion {

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Obtener todo el historial de Kardex
   * @return array Lista de registros en Kardex
   */
  public function getAll() {
    try {
      $query = "CALL spGetAllKardex()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Registrar un nuevo registro en Kardex
   * @param array $params Datos del Kardex
   * @return array Resultado de la operación
   */
  public function add($params = []) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spRegisterKardex(?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idproducto"],
        $params["fecha"],
        $params["stockmin"],
        $params["stockmax"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Registro de Kardex añadido correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Buscar un registro en Kardex por ID de producto
   * @param int $idproducto ID del producto
   * @return array Datos del Kardex encontrado
   */
  public function find($idproducto) {
    try {
      $query = "CALL spFindKardex(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idproducto]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar un registro en Kardex
   * @param array $params Datos del Kardex
   * @return array Resultado de la operación
   */
  public function update($params = []) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateKardex(?, ?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idkardex"],
        $params["idproducto"],
        $params["fecha"],
        $params["stockmin"],
        $params["stockmax"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Registro de Kardex actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Eliminar un registro de Kardex
   * @param int $idkardex ID del registro en Kardex a eliminar
   * @return array Resultado de la operación
   */
  public function delete($idkardex) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteKardex(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idkardex]);
      $resultado["status"] = true;
      $resultado["message"] = "Registro de Kardex eliminado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
