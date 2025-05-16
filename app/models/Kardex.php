<?php
// models/Kardex.php

require_once "Conexion.php";

class Kardex extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Obtiene el stock actual, stockmin y stockmax de un producto
     * @param int $idproducto
     * @return array|null ['stock_actual' => int, 'stockmin' => int, 'stockmax' => int] o null si error
     */
    public function getStockByProduct(int $idproducto)
    {
        try {
            $query = "CALL spStockActualPorProducto(?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idproducto]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            // Si no devuelve nada, retornamos null
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Kardex::getStockByProduct error: " . $e->getMessage());
            return null;
        }
    }
}