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
                    echo json_encode($venta->buscarProducto($termino));
                } else {
                    // Buscar clientes
                    $termino = $_GET['q'];
                    echo json_encode($venta->buscarCliente($termino));
                }
            } else {
                echo json_encode($venta->getAll());
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);

            if (isset($data['idcliente'], $data['tipocom'], $data['fechahora'], $data['numserie'], $data['numcom'], $data['moneda'], $data['detalleventa'])) {
                try {
                    // Llamar al método para registrar la venta
                    $venta->registrarVentaDetalle(
                        $data['idcliente'],
                        $data['tipocom'],
                        $data['fechahora'],
                        $data['numserie'],
                        $data['numcom'],
                        $data['moneda'],
                        $data['detalleventa']
                    );

                    echo json_encode(['status' => 'success', 'message' => 'Venta registrada correctamente.']);
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros en la solicitud.']);
            }
            break;

    }
}
?>