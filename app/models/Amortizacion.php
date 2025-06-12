<?php
// models/Amortizacion.php
require_once __DIR__ . '/Conexion.php';

class Amortizacion extends Conexion
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Crea una amortización para venta o compra.
     * Si es compra, inserta también un egreso.
     *
     * @param string $tipo             'venta' o 'compra'
     * @param int    $id               idventa o idcompra
     * @param int    $idformapago
     * @param float  $monto
     * @param string $numTrans         Opcional: número de transacción
     * @param int    $idadmin          quien registra el egreso
     * @param int    $idcolaborador    quien recibe el dinero
     * @param string $numcomprobante   opcional
     * @param string $justificacion    opcional
     * @return array
     * @throws Exception
     */
    public function create(
        string $tipo,
        int $id,
        int $idformapago,
        float $monto,
        int $idadmin,
        int $idcolaborador,
        ?string $numTrans = null,
        ?string $numcomprobante = null,
        ?string $justificacion = null
    ): array {
        // 1) Obtener info previa y validar monto
        $info = $this->obtenerInfo($tipo, $id);
        $saldoPrevio = (float) $info['total_pendiente'];
        if ($monto > $saldoPrevio) {
            throw new Exception("El monto de amortización no puede exceder el saldo restante");
        }

        // 2) Calcular nuevo saldo y estado
        $nuevoSaldo = $saldoPrevio - $monto;
        $estado = $nuevoSaldo <= 0 ? 'C' : 'P';

        // 3) Generar numTrans si no viene
        if (!$numTrans) {
            $numTrans = uniqid();
        }

        // 4) Iniciar transacción
        $this->pdo->beginTransaction();
        try {
            // 5) Insertar en amortizaciones
            $sqlA = "INSERT INTO amortizaciones
                (idventa, idcompra, idformapago, amortizacion, saldo, estado, numtransaccion)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $paramsA = $tipo === 'venta'
                ? [$id, null, $idformapago, $monto, $nuevoSaldo, $estado, $numTrans]
                : [null, $id, $idformapago, $monto, $nuevoSaldo, $estado, $numTrans];
            $stmtA = $this->pdo->prepare($sqlA);
            $stmtA->execute($paramsA);
            $lastId = $this->pdo->lastInsertId();

            // 6) Si es compra, insertar egreso
            if ($tipo === 'compra') {
                $sqlE = "INSERT INTO egresos
                    (idadmin, idcolaborador, idformapago, idcompra, concepto, monto, numcomprobante, justificacion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $concepto = "compra de insumos";
                $numcomprobante = $numcomprobante ?: $numTrans;
                $paramsE = [
                    $idadmin,
                    $idcolaborador,
                    $idformapago,
                    $id,
                    $concepto,
                    $monto,
                    $numcomprobante,
                    $justificacion
                ];
                $stmtE = $this->pdo->prepare($sqlE);
                $stmtE->execute($paramsE);
            }

            // 7) Commit
            $this->pdo->commit();

            // 8) Devolver datos de amortización
            return [
                'idamortizacion' => $lastId,
                $tipo === 'venta' ? 'idventa' : 'idcompra' => $id,
                'idformapago' => $idformapago,
                'amortizacion' => number_format($monto, 2, '.', ''),
                'saldo' => number_format($nuevoSaldo, 2, '.', ''),
                'numtransaccion' => $numTrans,
                'creado' => date('Y-m-d H:i:s')
            ];

        } catch (Exception $e) {
            // Rollback en caso de error
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene info de totales según tipo
     */
    public function obtenerInfo($tipo, $id)
    {
        if ($tipo === 'venta') {
            $sql = "SELECT total_original, total_pagado, total_pendiente
                    FROM vista_saldos_por_venta WHERE idventa = ?";
        } else {
            $sql = "SELECT total_original, total_pagado, total_pendiente
                    FROM vista_saldos_por_compra WHERE idcompra = ?";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: [
            'total_original' => 0,
            'total_pagado' => 0,
            'total_pendiente' => 0
        ];
    }

    /**
     * Lista amortizaciones según tipo y id
     */
    public function listBy($tipo, $id)
    {
        if ($tipo === 'venta') {
            $sql = "SELECT * FROM vista_amortizaciones_con_formapago
                    WHERE idventa = ? ORDER BY idamortizacion";
        } else {
            $sql = "SELECT * FROM vista_amortizaciones_con_formapago
                    WHERE idcompra = ? ORDER BY idamortizacion";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
