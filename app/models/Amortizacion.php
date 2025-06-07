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
     * Crea una amortización para venta o compra
     * @param string $tipo 'venta' o 'compra'
     */
    public function create($tipo, $id, $idformapago, $monto, $numTrans = null): array
    {
        // obtener info previa
        $info = $this->obtenerInfo($tipo, $id);
        $saldoPrevio = (float) $info['total_pendiente'];

        if ($monto > $saldoPrevio) {
            throw new Exception("El monto de amortización no puede exceder el saldo restante");
        }

        // calcular nuevo saldo y estado
        $nuevoSaldo = $saldoPrevio - $monto;
        $estado = $nuevoSaldo <= 0 ? 'C' : 'P';

        // generar numTrans
        if (!$numTrans) {
            $numTrans = uniqid();
        }
        /* $numTrans = uniqid(); */

        // insertar amortización
        $sql = "INSERT INTO amortizaciones
        (idventa, idcompra, idformapago, amortizacion, saldo, estado, numtransaccion)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($tipo === 'venta') {
            $params = [$id, null, $idformapago, $monto, $nuevoSaldo, $estado, $numTrans];
        } else {
            $params = [null, $id, $idformapago, $monto, $nuevoSaldo, $estado, $numTrans];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // devolver registro
        $lastId = $this->pdo->lastInsertId();
        return [
            'idamortizacion' => $lastId,
            $tipo === 'venta' ? 'idventa' : 'idcompra' => $id,
            'idformapago' => $idformapago,
            'amortizacion' => number_format($monto, 2, '.', ''),
            'saldo' => number_format($nuevoSaldo, 2, '.', ''),
            'numtransaccion' => $numTrans,
            'creado' => date('Y-m-d H:i:s')
        ];
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
            // totales de compra desde vistas
            $sql = "SELECT total_original, total_pagado, total_pendiente 
                    FROM vista_saldos_por_compra WHERE idcompra = ?";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: ['total_original' => 0, 'total_pagado' => 0, 'total_pendiente' => 0];
    }

    /**
     * Lista amortizaciones según tipo y id
     */
    public function listBy($tipo, $id)
    {
        if ($tipo === 'venta') {
            $sql = "SELECT * FROM vista_amortizaciones_con_formapago WHERE idventa = ? ORDER BY idamortizacion";
        } else {
            $sql = "SELECT * FROM vista_amortizaciones_con_formapago WHERE idcompra = ? ORDER BY idamortizacion";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}