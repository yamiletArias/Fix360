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

    // Obtener info de saldo y total de una venta
    public function obtenerInfoVenta($idventa)
    {
        $sql = "
        SELECT
          total_original,
          total_pagado,
          total_pendiente
        FROM vista_saldos_por_venta
        WHERE idventa = ?
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idventa]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_original' => 0,
            'total_pagado' => 0,
            'total_pendiente' => 0
        ];
    }

    // Consulta de amortizaciones por ID de venta
    public function listByVenta($idventa)
    {
        $sql = "SELECT * FROM vista_amortizaciones_con_formapago WHERE idventa = ? ORDER BY idamortizacion;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idventa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta una amortización y devuelve el registro creado.
     */
    public function create($idventa, $idformapago, $monto)
    {
        // 1) obtiene el saldo actual desde la vista
        $info = $this->obtenerInfoVenta($idventa);
        $saldoPrevio = (float) $info['total_pendiente'];

        if ($monto > $saldoPrevio) {
            throw new Exception("El monto de amortización no puede exceder el saldo restante");
        }

        //calculamos el nuevo saldo
        $nuevoSaldo = $saldoPrevio - $monto;

        //calculamos el estado
        $estado = $nuevoSaldo <= 0 ? 'C' : 'P';

        // 2) genera un número de transacción
        $numTrans = uniqid();

        // 3) ejecutamos el INSERT
        $sql = "INSERT INTO amortizaciones
        (idventa, idformapago, amortizacion, saldo, estado, numtransaccion)
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $idventa,
            $idformapago,
            $monto,
            $nuevoSaldo,
            $estado,
            $numTrans
        ]);

        // 4) devuelve el registro recien creado
        $id = $this->pdo->lastInsertId();
        return [
            'idamortizacion' => $id,
            'idventa' => $idventa,
            'idformapago' => $idformapago,
            'amortizacion' => number_format($monto, 2, '.', ''),
            'saldo' => number_format($nuevoSaldo, 2, '.', ''),
            'numtransaccion' => $numTrans,
            'creado' => date('Y-m-d H:i:s')
        ];
    }

}