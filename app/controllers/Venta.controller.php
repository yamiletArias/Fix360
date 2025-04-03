<?php
require_once '../models/Venta.php';

if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');

    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                if (isset($_GET['type']) && $_GET['type'] == 'producto') {
                    // Buscar productos
                    $termino = $_GET['q'];
                    echo json_encode($venta->buscarProducto($termino)); // Llamamos a la función buscarProducto
                } else {
                    // Buscar clientes
                    $termino = $_GET['q'];
                    echo json_encode($venta->buscarCliente($termino)); // Llamamos a la función buscarCliente
                }
            } else {
                echo json_encode($venta->getAll());
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (
                isset($data['tipocom']) && isset($data['numserie']) && isset($data['numcom']) &&
                isset($data['cliente']) && isset($data['fechahora']) && isset($data['moneda']) && isset($data['productos'])
            ){
                // Aquí captura correctamente los datos recibidos
                $tipocom = $_POST['tipocom'];
                $numserie = $_POST['numserie'];
                $numcom = $_POST['numcom'];
                $cliente = $_POST['cliente']; // Cliente Id
                $fechahora = $_POST['fechahora'];
                $moneda = $_POST['moneda'];
                $productos = $_POST['productos'];

                // Registrar la venta
                $resultado = $venta->registrarVenta($tipocom, $numserie, $numcom, 
                $cliente, $fechahora, $moneda, $productos);

                echo json_encode($resultado);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
            }
            break;
    }
}
