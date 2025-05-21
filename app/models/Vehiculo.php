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

    public function registerVehiculo($params = []): int
    {
        $numRows = 0;
        try {
            $query = "CALL spRegisterVehiculo( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
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

    public function getVehiculoByCliente($idcliente): array
    {
        $result = [];
        try {
            $query = "CALL spGetVehiculoByCliente(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idcliente]);
            $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
        return $result;
    }

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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetalleOrdenServicio(int $idorden): array
    {
        $stmt = $this->pdo->prepare("CALL spGetDetalleOrdenServicio(?)");
        $stmt->execute([$idorden]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUltimoKilometraje(int $idvehiculo): array
    {
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

    // ——— NUEVA FUNCIÓN 1 ———
    /**
     * Obtiene datos del vehículo junto con el idcliente y nombre de su propietario activo.
     *
     * @param int $idvehiculo
     * @return array   Un único arreglo asociativo con los campos del vehículo y:
     *                 - idcliente_propietario
     *                 - propietario
     */
    public function getVehiculoConPropietario(int $idvehiculo): array
    {
        try {
            $sql = "CALL spGetVehiculoConPropietario(?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idvehiculo]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener vehículo con propietario: " . $e->getMessage());
        }
    }

    // ——— NUEVA FUNCIÓN 2 ———
    /**
     * Actualiza el vehículo y registra cambio de propietario en la tabla propietarios.
     * Devuelve el idcliente que queda como propietario activo al final.
     *
     * Parámetros esperados en $params:
     *   - idvehiculo
     *   - idmodelo
     *   - idtcombustible
     *   - placa
     *   - anio
     *   - numserie
     *   - color
     *   - vin
     *   - numchasis
     *   - idcliente_nuevo
     *
     * @param array $params
     * @return array  Arreglo asociativo con ['idcliente_propietario_nuevo' => valor]
     */
    public function updateVehiculoConHistorico(array $params): array
    {
        $cmd = null;
        try {
            $sql = "CALL spUpdateVehiculoConHistorico( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
            $cmd = $this->pdo->prepare($sql);
            $cmd->execute([
                $params['idvehiculo'],
                $params['idmodelo'],
                $params['idtcombustible'],
                $params['placa'],
                $params['anio'],
                $params['numserie'],
                $params['color'],
                $params['vin'],
                $params['numchasis'],
                $params['idcliente_nuevo']
            ]);
            $row = $cmd->fetch(PDO::FETCH_ASSOC);
            return $row ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar vehículo con histórico: " . $e->getMessage());
        } finally {
            if ($cmd !== null) {
                $cmd->closeCursor();
            }
        }
    }
}
