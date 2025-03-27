<?php

require_once "../models/Conexion.php";

class Venta extends Conexion{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Registrar la venta principal
    public function registrarVenta($tipo, $numserie, $numcomprobante, $nomcliente, $fecha, $tipomoneda) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("CALL spRegistroVentas(?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tipo, $numserie, $numcomprobante, $nomcliente, $fecha, $tipomoneda]);
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception("Error al registrar la venta: " . $e->getMessage());
        }
    }

    // Registrar los productos asociados a la venta
    public function registrarProductos($productos, $precios, $cantidades, $descuentos, $numcomprobante) {
        try {
            for ($i = 0; $i < count($productos); $i++) {
                $producto = $productos[$i];
                $precio = $precios[$i];
                $cantidad = $cantidades[$i];
                $descuento = $descuentos[$i];

                $stmtDetalle = $this->pdo->prepare("CALL spRegistrarDetalleVenta(?, ?, ?, ?, ?)");
                $stmtDetalle->execute([$producto, $precio, $cantidad, $descuento, $numcomprobante]);
            }
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al registrar los productos: " . $e->getMessage());
        }
    }
}

?>
