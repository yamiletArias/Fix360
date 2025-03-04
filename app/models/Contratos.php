<?php

require_once "../models/Conexion.php";

/**
 * Clase ContratosModel
 * Maneja las operaciones CRUD de la tabla 'contratos'
 */
class Contratos extends Conexion {

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Obtener todos los contratos
   * @return array Lista de contratos
   */
  public function getAll() {
    try {
      $query = "CALL spListContratos()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Registrar un nuevo contrato
   * @param int $idrol ID del rol
   * @param int $idpersona ID de la persona
   * @param string $fechainicio Fecha de inicio del contrato
   * @param string $fechafin Fecha de finalización del contrato
   * @return array Resultado de la operación
   */
  public function add($idrol, $idpersona, $fechainicio, $fechafin) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spRegisterContrato(?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idrol, $idpersona, $fechainicio, $fechafin]);
      $resultado["status"] = true;
      $resultado["message"] = "Contrato registrado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Buscar un contrato por su ID
   * @param int $idcontrato ID del contrato
   * @return array Datos del contrato encontrado
   */
  public function find($idcontrato) {
    try {
      $query = "CALL spGetContratoById(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcontrato]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar un contrato
   * @param int $idcontrato ID del contrato a actualizar
   * @param int $idrol Nuevo ID del rol
   * @param int $idpersona Nuevo ID de la persona
   * @param string $fechainicio Nueva fecha de inicio
   * @param string $fechafin Nueva fecha de finalización
   * @return array Resultado de la operación
   */
  public function update($idcontrato, $idrol, $idpersona, $fechainicio, $fechafin) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateContrato(?, ?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcontrato, $idrol, $idpersona, $fechainicio, $fechafin]);
      $resultado["status"] = true;
      $resultado["message"] = "Contrato actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Eliminar un contrato
   * @param int $idcontrato ID del contrato a eliminar
   * @return array Resultado de la operación
   */
  public function delete($idcontrato) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteContrato(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcontrato]);
      $resultado["status"] = true;
      $resultado["message"] = "Contrato eliminado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
