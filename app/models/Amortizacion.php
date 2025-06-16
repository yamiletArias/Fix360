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

    // Método principal para registrar amortización o compra
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
        $saldoPrevio = 0;
        $nuevoSaldo = 0;
        $estado = 'P';

        if ($tipo === 'venta') {
            $info = $this->obtenerInfo($tipo, $id);
            $saldoPrevio = (float) $info['total_pendiente'];
            if ($monto > $saldoPrevio) {
                throw new Exception("El monto de amortización no puede exceder el saldo restante");
            }
            $nuevoSaldo = $saldoPrevio - $monto;
            $estado = $nuevoSaldo <= 0 ? 'C' : 'P';
        }

        if (!$numTrans) {
            $numTrans = uniqid();
        }

        $this->pdo->beginTransaction();
        try {
            // Registro para VENTAS
            if ($tipo === 'venta') {
                $sql = "INSERT INTO amortizaciones 
                        (idventa, idformapago, amortizacion, saldo, estado, numtransaccion)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$id, $idformapago, $monto, $nuevoSaldo, $estado, $numTrans]);
                $lastId = $this->pdo->lastInsertId();
            }

            // Registro para COMPRAS
            if ($tipo === 'compra') {
                $sqlE = "INSERT INTO egresos 
                         (idadmin, idcolaborador, idformapago, idcompra, concepto, monto, numcomprobante, justificacion)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $concepto = "Compra de insumos";
                $numcomprobante = $numcomprobante ?: $numTrans;
                $stmtE = $this->pdo->prepare($sqlE);
                $stmtE->execute([
                    $idadmin,
                    $idcolaborador,
                    $idformapago,
                    $id,
                    $concepto,
                    $monto,
                    $numcomprobante,
                    $justificacion
                ]);
            }

            $this->pdo->commit();

            // Retorno según tipo
            return $tipo === 'venta'
                ? [
                    'idamortizacion' => $lastId,
                    'idventa' => $id,
                    'idformapago' => $idformapago,
                    'amortizacion' => number_format($monto, 2, '.', ''),
                    'saldo' => number_format($nuevoSaldo, 2, '.', ''),
                    'numtransaccion' => $numTrans,
                    'creado' => date('Y-m-d H:i:s')
                ]
                : [
                    'egreso' => 'registrado',
                    'idcompra' => $id,
                    'monto' => number_format($monto, 2, '.', ''),
                    'numcomprobante' => $numcomprobante,
                    'justificacion' => $justificacion
                ];

        } catch (Exception $e) {
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
