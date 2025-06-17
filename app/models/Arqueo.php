<?php
// models/Arqueo.php
require_once __DIR__ . '/Conexion.php';

class Arqueo extends Conexion
{
  private $pdo;

  public function __construct()
  {
    $this->pdo = parent::getConexion();
  }

  public function getIngresosPorFecha(string $fecha): array
  {
    $sql = "
        SELECT
            f.formapago AS label,
            COALESCE(SUM(a.amortizacion), 0) AS valor
        FROM vista_formapagos f
        LEFT JOIN amortizaciones a
            ON a.idformapago = f.idformapago
            AND a.idventa IS NOT NULL
            AND DATE(a.creado) = :fecha
        GROUP BY f.idformapago, f.formapago
        ORDER BY f.idformapago 
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['fecha' => $fecha]);

    return array_map(
      fn($row) => ['label' => $row['label'], 'valor' => $row['valor']],
      $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
  }

  public function getEgresosPorFecha(string $fecha): array
  {
    $sql = "
        SELECT
            c.concepto AS label,
            COALESCE(SUM(e.monto), 0) AS valor
        FROM vista_conceptos_egresos c
        LEFT JOIN egresos e
            ON e.concepto = c.concepto
            AND DATE(e.creado) = :fecha
        GROUP BY c.concepto
        ORDER BY c.concepto
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['fecha' => $fecha]);

    return array_map(
      fn($row) => ['label' => $row['label'], 'valor' => $row['valor']],
      $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
  }

  public function getResumenPorFecha(string $fecha): array
  {
    $sql = "
      SELECT
        saldo_anterior,
        ingreso_efectivo,
        total_efectivo,
        total_egresos,
        total_caja
      FROM vista_resumen_arqueo
      WHERE fecha = :fecha
      LIMIT 1
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['fecha' => $fecha]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no hay fila (p.ej. no hay movimientos), devolvemos todo a cero
    if (!$row) {
      return [
        'saldo_anterior' => 0,
        'ingreso_efectivo' => 0,
        'total_efectivo' => 0,
        'total_egresos' => 0,
        'total_caja' => 0,
      ];
    }

    // Cast a float para el JSON
    return array_map(fn($v) => floatval($v), $row);
  }
}
