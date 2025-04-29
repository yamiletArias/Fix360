<?php
// app/models/OrdenServicio.php
require_once "../models/Conexion.php";

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
    public function registerOrden(array $p): int
    {
        try {
            // Iniciar transacción
            $this->pdo->beginTransaction();

            // 1) Insertar cabecera
            $stmt = $this->pdo->prepare(
                "CALL spuRegisterOrdenServicio(?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->execute([
                $p['idadmin'],           // Admin quien registra
                $p['idmecanico'],        // Mecánico asignado
                $p['idpropietario'],     // Propietario del vehículo
                $p['idcliente'],         // Cliente
                $p['idvehiculo'],        // Vehículo
                $p['kilometraje'],       // Kilometraje al ingreso
                $p['observaciones'],     // Observaciones
                $p['ingresogrua'] ? 1 : 0,// Flag ingreso grúa
                $p['fechaingreso'],      // Fecha y hora de ingreso
                $p['fecharecordatorio']  // Fecha de recordatorio
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $idorden = $row['idorden'] ?? 0;
            $stmt->closeCursor();

            if ($idorden <= 0) {
                $this->pdo->rollBack();
                return 0;
            }

            // 2) Insertar cada detalle
            $stmtDetalle = $this->pdo->prepare(
                "CALL spuInsertDetalleOrdenServicio(?,?,?)"
            );
            foreach ($p['detalle'] as $item) {
                $stmtDetalle->execute([
                    $idorden,
                    intval($item['idservicio']),
                    floatval($item['precio'])
                ]);
                // Liberar cursor para próxima llamada
                $stmtDetalle->closeCursor();
            }

            // Confirmar transacción
            $this->pdo->commit();
            return $idorden;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("OrdenServicio::registerOrden error: " . $e->getMessage());
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
    public function listarPorPeriodo(string $modo, string $fecha): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL spListOrdenesPorPeriodo(:modo, :fecha)");
            $stmt->execute([
                ':modo'  => $modo,
                ':fecha' => $fecha,
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("OrdenServicio::listarPorPeriodo error: " . $e->getMessage());
            return [];
        }
    }
}
