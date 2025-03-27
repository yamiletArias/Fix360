<?php
require_once '../models/Conexion.php';
require_once '../models/Venta.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener la conexión usando el método estático de la clase Conexion
        $conexion = Conexion::getConexion();

        // Crear una instancia de la clase Venta pasando la conexión
        $venta = new Venta($conexion);

        // Obtener los datos del POST
        $tipo = $_POST['tipo'];
        $numserie = $_POST['numserie'] ?: 'V' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT); 
        $numcomprobante = 'C' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT); 

        $nomcliente = $_POST['nomcliente'];
        $fecha = $_POST['fecha'];
        $tipomoneda = $_POST['tipomoneda'];

        $productos = $_POST['producto']; 
        $precios = $_POST['precio']; 
        $cantidades = $_POST['cantidad']; 
        $descuentos = isset($_POST['descuento']) ? $_POST['descuento'] : [];

        // Registrar la venta
        $venta->registrarVenta($tipo, $numserie, $numcomprobante, $nomcliente, $fecha, $tipomoneda);

        // Registrar los productos
        $venta->registrarProductos($productos, $precios, $cantidades, $descuentos, $numcomprobante);

        // Calcular el total de la venta
        $totalVenta = array_sum(array_map(function($precio, $cantidad, $descuento) {
            return $precio * $cantidad - $descuento;
        }, $precios, $cantidades, $descuentos));

        // Responder con los datos de la venta
        echo json_encode([
            'success' => 'Venta registrada exitosamente',
            'venta' => [
                'tipo' => $tipo,
                'numcomprobante' => $numcomprobante,
                'fecha' => $fecha,
                'importe' => number_format($totalVenta, 2),
            ]
        ]);
    } catch (Exception $e) {
        // Manejo de excepciones en caso de error
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
