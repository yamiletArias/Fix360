<?php
// app/models/Movimiento.php
require_once "../models/Conexion.php";

class Movimiento extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Obtiene el stock actual de un producto dado su ID
     *
     * @param int $idproducto
     * @return mixed|null  Stock actual o null si no existe
     */
    public function obtenerStockActual(int $idproducto)
    {
        try {
            $stmt = $this->pdo->prepare("CALL spStockActualPorProducto(:idproducto)");
            $stmt->execute([':idproducto' => $idproducto]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result['stock_actual'] ?? null;
        } catch (Exception $e) {
            error_log("Movimiento::obtenerStockActual error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista los movimientos de un producto en un periodo
     *
     * @param int    $idproducto
     * @param string $modo    'dia' | 'semana' | 'mes'
     * @param string $fecha   Formato 'YYYY-MM-DD'
     * @return array
     */
    public function listarMovimientosPorPeriodo(int $idproducto, string $modo, string $fecha): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL spListMovimientosPorProductoPorPeriodo(:idproducto, :modo, :fecha)");
            $stmt->execute([
                ':idproducto' => $idproducto,
                ':modo'       => $modo,
                ':fecha'      => $fecha,
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("Movimiento::listarMovimientosPorPeriodo error: " . $e->getMessage());
            return [];
        }
    }
}
