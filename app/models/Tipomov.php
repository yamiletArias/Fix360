<?php

require_once "../models/Conexion.php";

/**
 * Clase Tipomovimiento
 * Maneja las operaciones CRUD de la tabla 'tipomovimientos'
 */
class Tipomovimiento extends Conexion {

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Obtener todos los tipos de movimiento
   * @return array Lista de tipos de movimiento
   */
  public function getAll() {
    try {
      $query = "CALL spGetAllTipomovimientos()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Registrar un nuevo tipo de movimiento
   * @param array $params Datos del tipo de movimiento
   * @return array Resultado de la operación
   */
  public function add($params = []) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spRegisterTipomovimiento(?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["flujo"],
        $params["tipomov"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Tipo de movimiento registrado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Buscar tipo de movimiento por ID
   * @param int $idtipomov ID del tipo de movimiento
   * @return array Datos encontrados
   */
  public function find($params = []) {
    try {
      $query = "CALL spFindTipomovimiento(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idtipomov"]
      ]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar un tipo de movimiento
   * @param array $params Datos del tipo de movimiento
   * @return array Resultado de la operación
   */
  public function update($params = []) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateTipomovimiento(?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idtipomov"],
        $params["flujo"],
        $params["tipomov"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Tipo de movimiento actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Eliminar un tipo de movimiento
   * @param int $idtipomov ID del tipo de movimiento a eliminar
   * @return array Resultado de la operación
   */
  public function delete($params = []) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteTipomovimiento(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idtipomov"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Tipo de movimiento eliminado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
