<?php
require_once "../models/Conexion.php";

class Cliente extends Conexion {

  private $pdo;

  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Registra un cliente en la base de datos utilizando un procedimiento almacenado.
   * No maneja transacciones aquí porque ya están en el SP.
   * 
   * @param array $params Arreglo con los datos del cliente.
   * @return array Retorna el resultado de la operación o un mensaje de error.
   */
  public function registerCliente($params = []): array {
    try {
      $query = "CALL spRegisterCliente(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["tipo"],
        $params["nombres"],
        $params["apellidos"],
        $params["tipodoc"],
        $params["numdoc"],
        $params["direccion"],
        $params["correo"],
        $params["telprincipal"],
        $params["telalternativo"],
        $params["nomcomercial"],
        $params["razonsocial"],
        $params["telefono"],
        $params["ruc"],
        $params["idcontactabilidad"]
      ]);

      $result = $cmd->fetch(PDO::FETCH_ASSOC);
      $cmd->closeCursor();
      
      return $result ?: ["message" => "Registro exitoso"];

    } catch (PDOException $e) {
      error_log("Error DB: " . $e->getMessage());
      return ["error" => "Error en la base de datos"];
    } catch (Exception $e) {
      error_log("Error del servidor: " . $e->getMessage());
      return ["error" => "Error inesperado en el servidor"];
    }
  }
}
