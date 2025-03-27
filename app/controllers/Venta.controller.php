<?php
require_once '../config/Server.php';
require_once '../models/Venta.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO(SGBD, USER, PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $tipo = $_POST['tipo'];

        $numserie = $_POST['numserie'] ?: 'V' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT); // o se puede calcular según un patrón
        $numcomprobante = 'C' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT); // O lo generas según la lógica de tu base de datos

        //$numserie = isset($_POST['numserie']) && !empty($_POST['numserie']) ? $_POST['numserie'] : null;
        //$numcomprobante = $_POST['numcomprobante'];

        $nomcliente = $_POST['nomcliente'];
        $fecha = $_POST['fecha'];
        $tipomoneda = $_POST['tipomoneda'];

        $productos = $_POST['producto']; 
        $precios = $_POST['precio']; 
        $cantidades = $_POST['cantidad']; 
        $descuentos = isset($_POST['descuento']) ? $_POST['descuento'] : [];

        $venta = new Venta($pdo);

        $venta->registrarVenta($tipo, $numserie, $numcomprobante, $nomprovedor, $fecha, $tipomoneda);
        $venta->registrarProductos($productos, $precios, $cantidades, $descuentos, $numcomprobante);


        $totalVenta = array_sum(array_map(function($precio, $cantidad, $descuento) {
            return $precio * $cantidad - $descuento;
        }, $precios, $cantidades, $descuentos));

        // Devolver la nueva venta
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
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
