<?php

require_once "../models/Conexion.php";

/**
 * Clase ClientesModel
 * Maneja las operaciones CRUD de la tabla 'clientes'
 */
class Clientes extends Conexion {

  protected $pdo;

  /**
   * Constructor
   * Obtiene la conexión a la base de datos.
   */
  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Obtener todos los clientes con la información de la empresa o persona asociada
   * @return array Lista de clientes
   */
  public function getAll() {
    try {
      $query = "CALL spListClientes()";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Registrar un nuevo cliente
   * @param int|null $idempresa ID de la empresa (puede ser NULL)
   * @param int|null $idpersona ID de la persona (puede ser NULL)
   * @param int $idcontactabilidad ID de la forma de contacto
   * @return array Resultado de la operación
   */
  public function add($idempresa, $idpersona, $idcontactabilidad) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spRegisterCliente(?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idempresa, $idpersona, $idcontactabilidad]);
      $resultado["status"] = true;
      $resultado["message"] = "Cliente registrado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Buscar un cliente por su ID
   * @param int $idcliente ID del cliente
   * @return array Datos del cliente encontrado
   */
  public function find($idcliente) {
    try {
      $query = "CALL spGetClienteById(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcliente]);
      return $cmd->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Actualizar un cliente
   * @param int $idcliente ID del cliente a actualizar
   * @param int|null $idempresa Nuevo ID de la empresa asociada (puede ser NULL)
   * @param int|null $idpersona Nuevo ID de la persona asociada (puede ser NULL)
   * @param int $idcontactabilidad Nuevo ID de la forma de contacto
   * @return array Resultado de la operación
   */
  public function update($idcliente, $idempresa, $idpersona, $idcontactabilidad) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spUpdateCliente(?, ?, ?, ?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcliente, $idempresa, $idpersona, $idcontactabilidad]);
      $resultado["status"] = true;
      $resultado["message"] = "Cliente actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  /**
   * Eliminar un cliente
   * @param int $idcliente ID del cliente a eliminar
   * @return array Resultado de la operación
   */
  public function delete($idcliente) {
    $resultado = ["status" => false, "message" => ""];
    try {
      $query = "CALL spDeleteCliente(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$idcliente]);
      $resultado["status"] = true;
      $resultado["message"] = "Cliente eliminado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}
