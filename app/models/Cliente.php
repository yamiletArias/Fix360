<?php
require_once "../models/Conexion.php";

class Cliente extends Conexion {
  private $pdo;

  public function __CONSTRUCT(){
    $this->pdo = parent::getConexion();
  }

  /**
   * Para esta funcion se utilizara una Transaccion.
   * Que en PHP sirve para que una accion no se realize si no es que se ejecuta correctamente
   * si no logra hacerlo, se hace un rollback y mantiene los datos intactos 
   * Se tienen 2 Catch para saber con exactitud si fue un error o de la DB o del servidor
   */
  public function registerCliente($params = []): int {
    $this->pdo->beginTransaction(); 

    try {
      $cmd = $this->pdo->prepare("CALL spRegisterCliente(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
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
      $this->pdo->commit();

      return $result['idcliente'];

    } catch (Exception $e){
      $this->pdo->rollBack();
      error_log("Error del servidor: " . $e->getMessage());

      return -1;
    }
    catch(PDOException $e) {
      $this->pdo->rollBack();
      error_log("Error DB: " - $e->getMessage());
      return -1;  
    }
  }
}