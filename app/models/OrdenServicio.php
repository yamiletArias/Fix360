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
     * Registra la orden (cabecera + detalle) usando SP único
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
            // Convertir detalle a JSON
            $jsonDet = json_encode($p['detalle'], JSON_UNESCAPED_UNICODE);

            // Llamada al SP que inserta cabecera y detalle en una sola transacción
            $stmt = $this->pdo->prepare(
                "CALL spRegistrarOrdenServicio(?,?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->execute([
                $p['idadmin'],           // Admin quien registra
                $p['idmecanico'],        // Mecánico asignado
                $p['idpropietario'],     // Propietario del vehículo
                $p['idcliente'],         // Cliente (empresa o persona)
                $p['idvehiculo'],        // Vehículo
                $p['kilometraje'],       // Kilometraje al ingreso
                $p['observaciones'],     // Observaciones
                $p['ingresogrua'] ? 1 : 0,// Flag ingreso grúa
                $p['fechaingreso'],      // Fecha y hora de ingreso
                $p['fecharecordatorio'], // Fecha de recordatorio
                $jsonDet                 // Detalle como JSON
            ]);

            // Obtener el nuevo ID retornado por el SP
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $nuevoId = $row['nuevoIdOrden'] ?? 0;
            $stmt->closeCursor();

            return $nuevoId;

        } catch (Exception $e) {
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
