<?php

require_once "../models/Conexion.php";

/**
 * Clase MarcasModel
 * Maneja las operaciones CRUD de la tabla 'marcas'
 */
class Marca extends Conexion {

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT() {
    $this->pdo = Conexion::getConexion();
  }

  /**
   * Obtener todas las marcas
   * @return array Lista de marcas que solo son de vehiculos
   */
  public function getAllMarcaVehiculo():array {
    $result = [];
    try {
      $query = "CALL spGetAllMarcaVehiculo()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }

    return $result;
  }

  public function getAllMarcaProducto():array {
    $result = [];
    try {
      $query = "CALL spGetAllMarcaProducto()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
      die($e->getMessage());
    }

    return $result;

  }

}
