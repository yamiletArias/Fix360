<?php
// app/models/OrdenServicio.php
require_once __DIR__ . '/Conexion.php';

class OrdenServicio extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Registra la orden (cabecera + detalle) usando SP separados
     * @param array $p {
     *   @var int    idadmin
     *   @var int    idmecanico
     *   @var int    idpropietario
     *   @var int    idcliente
     *   @var int    idvehiculo
     *   @var float  kilometraje
     *   @var string observaciones
     *   @var bool   ingresogrua
     *   @var string fechaingreso  Formato 'YYYY-MM-DD HH:MM:SS'
     *   @var string fecharecordatorio Formato 'YYYY-MM-DD'
     *   @var array  detalle      Array de {idservicio, precio}
     * }
     * @return int ID de la orden (0 si falla)
     */
    public function registerOrdenServicio($params = []): int
    {
        try {
            $pdo= $this->pdo;
            $pdo->beginTransaction();
            error_log("Parametros para spRegisterOrdenServicio: ". print_r($params,true));

            $stmtOrden = $pdo->prepare("CALL spRegisterOrdenServicio(?,?,?,?,?,?,?,?)");
            $stmtOrden->execute([
                $params["idadmin"],
                $params["idpropietario"],
                $params["idcliente"],
                $params["idvehiculo"],
                $params["kilometraje"],
                $params["observaciones"],
                $params["ingresogrua"],
                $params["fechaingreso"]
            ]);
            error_log("Sp SpRegisterOrdenServicio ejecutado");

            $result = [];
            do{
                $tmp = $stmtOrden->fetch(PDO::FETCH_ASSOC);
                error_log("Resultado fetch: " . print_r($tmp,true));
                if($tmp && isset($tmp['idorden'])){
                    $result = $tmp;
                    break;
                }
            } while($stmtOrden->nextRowset());
            $stmtOrden->closeCursor();

            $idorden = $result['idorden'] ?? 0;

            if (!$idorden){
                error_log("SP ejecutado pero no devolvio ID de orden");
                throw new Exception("No se pudo obtener el id de orden");
            }
            $stmtDetalle = $pdo->prepare("CALL spInsertDetalleOrdenServicio(?,?,?,?)");

            foreach ($params['servicios'] as $servicio){          
                $stmtDetalle->execute([
                    $idorden,
                    $servicio["idservicio"],
                    $servicio["idmecanico"],
                    $servicio["precio"]
                ]);
            }
            $pdo->commit();
            error_log("Orden registrada con id: " . $idorden);
            return $idorden;

            } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error DB: " . $e->getMessage());
            return 0;
      
          } catch (Exception $ex) {
            error_log("Error general: " . $ex->getMessage());
            return 0;
          }
    }
    /**
     * Lista órdenes por periodo: 'dia', 'semana' o 'mes'
     *
     * @param string $modo  'dia' | 'semana' | 'mes'
     * @param string $fecha Fecha en formato 'YYYY-MM-DD'
     * @return array
     */
    public function listarPorPeriodo(string $modo, string $fecha, string $estado = 'A'): array{
         try {     
            $stmt = $this->pdo->prepare("CALL spListOrdenesPorPeriodo(:modo, :fecha, :estado)");
            $stmt->execute([
                ':modo'   => $modo,
                ':fecha'  => $fecha,
                ':estado' => $estado,
             ]);
             $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
             $stmt->closeCursor();
             return $result;
        } catch (Exception $e) {
            error_log("OrdenServicio::listarPorPeriodo error: " . $e->getMessage());
            return [];
        }
    }

    public function setFechaSalida(int $idorden): int {
    try {
        $pdo = $this->pdo;
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("CALL spInsertFechaSalida(:idorden)");
        $stmt->execute([':idorden' => $idorden]);
        $affected = $stmt->rowCount();
        $stmt->closeCursor();
        $pdo->commit();
        return $affected;  
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("OrdenServicio::setFechaSalida error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Obtiene todos los datos de una orden (cabecera, detalle y total) desde el SP
 *
 * @param int $idorden
 * @return array{
 *   cabecera: array<string,mixed>,
 *   detalle: array<array<string,mixed>>,
 *   total: float
 * }
 */
public function getDetalleOrden(int $idorden): array
{
    $pdo = $this->pdo;
    try {
        $stmt = $pdo->prepare("CALL spGetDetalleOrdenServicio(:idorden)");
        $stmt->execute([':idorden' => $idorden]);

        $resultSets = [];
        // Recorremos cada resultset que devuelve el SP
        do {
            $resultSets[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());
        $stmt->closeCursor();

        // Desempaquetamos: 0 = cabecera, 1 = detalle, 2 = total
        $cabecera = $resultSets[0][0] ?? [];
        $detalle  = $resultSets[1]    ?? [];
        $total    = isset($resultSets[2][0]['total_orden'])
                  ? (float)$resultSets[2][0]['total_orden']
                  : 0.0;

        return [
            'cabecera' => $cabecera,
            'detalle'  => $detalle,
            'total'    => $total,
        ];
    } catch (\PDOException $e) {
        error_log("OrdenServicio::getDetalleOrden error: " . $e->getMessage());
        return [
            'cabecera' => [],
            'detalle'  => [],
            'total'    => 0.0,
        ];
    }
}

/**
 * Marca una orden como eliminada (soft–delete) y registra la justificación.
 *
 * @param int    $idorden
 * @param string $justificacion
 * @return int  Número de filas afectadas (1 si tuvo éxito, 0 si no)
 */
public function deleteOrdenServicio(int $idorden, string $justificacion): int
{
    try {
        $stmt = $this->pdo->prepare("CALL spDeleteOrdenServicio(:idorden, :justificacion)");
        $stmt->execute([
            ':idorden'       => $idorden,
            ':justificacion' => $justificacion
        ]);
        $affected = $stmt->rowCount();
        $stmt->closeCursor();
        return $affected;
    } catch (\PDOException $e) {
        error_log("OrdenServicio::deleteOrdenServicio error: " . $e->getMessage());
        return 0;
    }
}

/**
     * Lista órdenes de servicio para un vehículo en un rango (mes, semestral, anual).
     *
     * @param string $modo         'mes'|'semestral'|'anual'
     * @param string $fecha        Fecha de referencia 'YYYY-MM-DD'
     * @param string $estado       'A'|'D'
     * @param int    $idvehiculo
     * @return array
     */
    public function listarHistorialPorVehiculo(string $modo, string $fecha, string $estado, int $idvehiculo): array
    {
        try {
            $sql = "CALL spHistorialOrdenesPorVehiculo(:modo, :fecha, :estado, :idvehiculo)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':modo'       => $modo,
                ':fecha'      => $fecha,
                ':estado'     => $estado,
                ':idvehiculo' => $idvehiculo,
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("OrdenServicio::listarHistorialPorVehiculo error: " . $e->getMessage());
            return [];
        }
    }

        /**
     * Devuelve el total de órdenes activas (fechasalida IS NULL y estado = 'A')
     *
     * @return int
     */
    public function getTotalOrdenesActivas(): int
    {
        try {
            $stmt = $this->pdo->query("SELECT total_ordenes_activas FROM v_total_ordenes_activas");
            $row  = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['total_ordenes_activas'] : 0;
        } catch (\PDOException $e) {
            error_log("OrdenServicio::getTotalOrdenesActivas error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Devuelve el total de órdenes ingresadas HOY (estado = 'A' y DATE(fechaingreso)=CURDATE())
     *
     * @return int
     */
    public function getTotalOrdenesHoy(): int
    {
        try {
            $stmt = $this->pdo->query("SELECT total_ordenes_hoy FROM vista_total_ordenes_hoy");
            $row  = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['total_ordenes_hoy'] : 0;
        } catch (\PDOException $e) {
            error_log("OrdenServicio::getTotalOrdenesHoy error: " . $e->getMessage());
            return 0;
        }
    }

    

}
