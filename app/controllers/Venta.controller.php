<?php
// Requiere la conexión a la base de datos y la clase Venta
require_once '../models/Venta.php';

if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');

    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                // Si se pasa un término 'q', verificar si es para productos o clientes
                if (isset($_GET['type']) && $_GET['type'] == 'producto') {
                    // Buscar productos basado en el término de búsqueda
                    $termino = $_GET['q'];
                    echo json_encode($venta->buscarProducto($termino)); // Llamamos a la función buscarProducto
                } else {
                    // Buscar clientes basado en el término de búsqueda
                    $termino = $_GET['q'];
                    echo json_encode($venta->buscarCliente($termino)); // Llamamos a la función buscarCliente
                }
            } else {
                // Si no hay búsqueda, obtener todas las ventas o productos (según el caso)
                echo json_encode($venta->getAll());
            }
            break;

        case 'POST':
            if (
                isset($_POST['tipo']) && isset($_POST['numserie']) && isset($_POST['numcom']) &&
                isset($_POST['cliente']) && isset($_POST['fechahora']) && isset($_POST['tipomoneda']) && isset($_POST['productos'])
            ) {

                // Obtener los datos del formulario
                $tipo_comprobante = $_POST['tipo'];
                $numero_serie = $_POST['numserie'];
                $numero_comprobante = $_POST['numcom'];
                $cliente = $_POST['cliente'];  // Este debería ser el ID del cliente
                $fecha = $_POST['fechahora'];
                $moneda = $_POST['moneda'];
                $productos = $_POST['productos'];  // Este será un array con los productos (producto_id, precio, cantidad, descuento, importe)

                // Registrar la venta y los productos
                $resultado = $venta->registrarVenta($tipo_comprobante, $numero_serie, $numero_comprobante, $cliente, $fecha, $moneda, $productos);

                // Enviar respuesta al cliente
                echo json_encode($resultado);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
            }
            break;
    }
}
