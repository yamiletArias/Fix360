<?php
require_once "../models/Conexion.php";

class Vehiculo extends Conexion
{

  private $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

  public function getAll(): array
  {
    $result = [];
    try {
      $sql = "SELECT * FROM vwVehiculos";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      throw new Exception($e->getMessage());
    }
    return $result;
  }

  /**
   * Registra el vehículo y asigna al propietario (persona o empresa) utilizando el procedimiento almacenado.
   *
   * @param array $params Datos para el registro:
   *   - idmodelo
   *   - placa
   *   - anio
   *   - kilometraje
   *   - numserie
   *   - color
   *   - tipocombustible
   *   - criterio         (valor de búsqueda, ej. DNI o RUC)
   *   - tipoBusqueda     (ej. 'DNI', 'RUC', etc.)
   *
   * @return int Número de filas afectadas o 0 en caso de error.
   */
  public function registerVehiculo($params = []): int
  {
    $numRows = 0;
    try {
      $query = "CALL spRegisterVehiculo( ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // Agregar un "?" más para idcliente
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([
        $params["idmodelo"],
        $params["idtcombustible"],
        $params["placa"],
        $params["anio"],
        $params["numserie"],
        $params["color"],
        $params["vin"],
        $params["numchasis"],
        $params["idcliente"] // Agregar este campo
      ]);

      $numRows = $stmt->rowCount();
    } catch (PDOException $e) {
      error_log("Error DB: " . $e->getMessage());
      return $numRows;
    }
    return $numRows;
  }

  public function getVehiculoByCliente($idcliente): array{
    $result = [];
    try {
      $query = "CALL spGetVehiculoByCliente(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute(
        array($idcliente)
      );
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }

    return $result;
  }
}
