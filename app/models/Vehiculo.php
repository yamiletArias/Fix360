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
        $params["idcliente"]
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

  /**
   * Obtiene todas las órdenes de servicio de un vehículo
   *
   * @param int $idvehiculo
   * @return array
   */
  public function getOrdenesByVehiculo(int $idvehiculo): array
  {
    try {
      $sql  = "CALL spGetOrdenesByVehiculo(?)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$idvehiculo]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("Error al obtener órdenes: " . $e->getMessage());
    }
  }

  /**
   * Obtiene todas las ventas de un vehículo
   *
   * @param int $idvehiculo
   * @return array
   */
  public function getVentasByVehiculo(int $idvehiculo): array
  {
    try {
      $sql  = "CALL spGetVentasByVehiculo(?)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$idvehiculo]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("Error al obtener ventas: " . $e->getMessage());
    }
  }

  public function getJustificacionByOrden(int $idorden): array
  {
    $stmt = $this->pdo->prepare("CALL spGetJustificacionByOrden(?)");
    $stmt->execute([$idorden]);
    // devuelve un array con ['justificacion' => '...']
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getDetalleOrdenServicio(int $idorden): array
  {
    $stmt = $this->pdo->prepare("CALL spGetDetalleOrdenServicio(?)");
    $stmt->execute([$idorden]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getUltimoKilometraje(int $idvehiculo): array {
    $cmd = null;
    try {
        $sql = "CALL spGetUltimoKilometraje(?)";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$idvehiculo]);
        $row = $cmd->fetch(PDO::FETCH_ASSOC);
        return $row ?: [];
    } catch (PDOException $e) {
        error_log("Error en getUltimoKilometraje(): " . $e->getMessage());
        return [];
    } finally {
        if ($cmd !== null) {
            $cmd->closeCursor();
        }
    }
}

}
