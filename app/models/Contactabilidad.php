<?php

require_once "../models/Conexion.php";

/**
 * Clase ContactabilidadModel
 * Maneja las operaciones CRUD de la tabla 'contactabilidad'
 */
class Contactabilidad extends Conexion {

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Obtener todas las opciones de contactabilidad
   * @return array Lista de opciones de contactabilidad
   */
  public function getAll() {
    try {
      $query = "CALL spListContactabilidad()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Registrar una nueva opción de contactabilidad
   * @param string $contactabilidad Nombre del tipo de contactabilidad
   * @return array Resultado de la operación
   */
  public function add($contactabilidad) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spRegisterContactabilidad(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$contactabilidad]);
      $resultado["status"] = true;
      $resultado["message"] = "Tipo de contactabilidad registrado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Buscar una opción de contactabilidad por ID
   * @param int $idcontactabilidad ID de la contactabilidad
   * @return array Datos de la contactabilidad encontrada
   */
  public function find($idcontactabilidad) {
    try {
      $query = "CALL spGetContactabilidadById(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcontactabilidad]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar una opción de contactabilidad
   * @param int $idcontactabilidad ID del tipo de contactabilidad
   * @param string $contactabilidad Nuevo valor del tipo de contactabilidad
   * @return array Resultado de la operación
   */
  public function update($idcontactabilidad, $contactabilidad) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateContactabilidad(?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcontactabilidad, $contactabilidad]);
      $resultado["status"] = true;
      $resultado["message"] = "Tipo de contactabilidad actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Eliminar una opción de contactabilidad
   * @param int $idcontactabilidad ID del tipo de contactabilidad a eliminar
   * @return array Resultado de la operación
   */
  public function delete($idcontactabilidad) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteContactabilidad(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcontactabilidad]);
      $resultado["status"] = true;
      $resultado["message"] = "Tipo de contactabilidad eliminado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
