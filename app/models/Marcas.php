<?php

require_once "../models/Conexion.php";

/**
 * Clase MarcasModel
 * Maneja las operaciones CRUD de la tabla 'marcas'
 */
class Marcas extends Conexion {

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Obtener todas las marcas
   * @return array Lista de marcas
   */
  public function getAll() {
    try {
      $query = "CALL spListMarcas()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Registrar una nueva marca
   * @param string $nombre Nombre de la marca
   * @param string $tipo Tipo de la marca
   * @return array Resultado de la operación
   */
  public function add($nombre, $tipo) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spRegisterMarca(?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$nombre, $tipo]);
      $resultado["status"] = true;
      $resultado["message"] = "Marca registrada correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Buscar una marca por su ID
   * @param int $idmarca ID de la marca
   * @return array Datos de la marca encontrada
   */
  public function find($idmarca) {
    try {
      $query = "CALL spGetMarcaById(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idmarca]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar una marca
   * @param int $idmarca ID de la marca a actualizar
   * @param string $nombre Nuevo nombre de la marca
   * @param string $tipo Nuevo tipo de la marca
   * @return array Resultado de la operación
   */
  public function update($idmarca, $nombre, $tipo) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateMarca(?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idmarca, $nombre, $tipo]);
      $resultado["status"] = true;
      $resultado["message"] = "Marca actualizada correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Eliminar una marca
   * @param int $idmarca ID de la marca a eliminar
   * @return array Resultado de la operación
   */
  public function delete($idmarca) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteMarca(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idmarca]);
      $resultado["status"] = true;
      $resultado["message"] = "Marca eliminada correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
