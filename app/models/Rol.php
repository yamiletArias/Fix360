<?php

require_once "../models/Conexion.php";

/**
 * Clase RolesModel
 * Maneja las operaciones CRUD de la tabla 'roles'
 */
class Roles extends Conexion {

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Obtener todos los roles
   * @return array Lista de roles
   */
  public function getAll() {
    try {
      $query = "SELECT * FROM vwRoles";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Registrar un nuevo rol
   * @param string $rol Nombre del rol
   * @return array Resultado de la operación
   */
  public function add($rol) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spRegisterRol(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$rol]);
      $resultado["status"] = true;
      $resultado["message"] = "Rol registrado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Buscar un rol por su ID
   * @param int $idrol ID del rol
   * @return array Datos del rol encontrado
   */
  public function find($idrol) {
    try {
      $query = "CALL spGetRolById(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idrol]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar un rol
   * @param int $idrol ID del rol a actualizar
   * @param string $rol Nuevo nombre del rol
   * @return array Resultado de la operación
   */
  public function update($idrol, $rol) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateRol(?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idrol, $rol]);
      $resultado["status"] = true;
      $resultado["message"] = "Rol actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Eliminar un rol
   * @param int $idrol ID del rol a eliminar
   * @return array Resultado de la operación
   */
  public function delete($idrol) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteRol(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idrol]);
      $resultado["status"] = true;
      $resultado["message"] = "Rol eliminado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
