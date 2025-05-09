<?php
require_once __DIR__ . '/Conexion.php';

class Amortizacion extends Conexion
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Inserta una nueva amortización y calcula el saldo.
     */
    public function create(int $idventa, int $idformapago, float $monto): bool
    {
        // Suma de amortizaciones previas
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(amortizacion), 0) 
               FROM amortizaciones 
              WHERE idventa = ?"
        );
        $stmt->execute([$idventa]);
        $pagado = (float) $stmt->fetchColumn();

        // Calcular nuevo saldo
        $stmt = $this->pdo->prepare(
            "SELECT total 
               FROM vista_total_por_venta 
              WHERE idventa = ?"
        );
        $stmt->execute([$idventa]);
        $totalVenta = (float) $stmt->fetchColumn();

        $nuevoSaldo = max(0, $totalVenta - ($pagado + $monto));

        // Insertar amortización
        $ins = $this->pdo->prepare(
            "INSERT INTO amortizaciones
                (idventa, idformapago, amortizacion, saldo)
             VALUES (?, ?, ?, ?)"
        );
        return $ins->execute([$idventa, $idformapago, $monto, $nuevoSaldo]);
    }

    /**
     * Lista todas las amortizaciones de una venta.
     */
    public function listByVenta(int $idventa): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.idamortizacion, a.creado, f.formapago, a.amortizacion, a.saldo
               FROM amortizaciones a
               JOIN formapagos f USING(idformapago)
              WHERE a.idventa = ?
              ORDER BY a.creado DESC"
        );
        $stmt->execute([$idventa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el total neto de la venta desde la vista `vista_total_por_venta`.
     */
    public function getTotalPorVenta(int $idventa): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT total 
               FROM vista_total_por_venta 
              WHERE idventa = ?"
        );
        $stmt->execute([$idventa]);
        return (float) $stmt->fetchColumn() ?: 0.0;
    }
}
