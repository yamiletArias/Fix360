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
            $inputData = json_decode(file_get_contents('php://input'), true);

            // Validar que la acción esté definida
            if (isset($inputData['action']) && $inputData['action'] === 'finalizarVenta') {
                // Validación de campos requeridos
                if (empty($inputData['idcliente']) || empty($inputData['tipocom']) || empty($inputData['fechahora']) ||
                    empty($inputData['numserie']) || empty($inputData['numcom']) || empty($inputData['moneda']) ||
                    empty($inputData['detalle'])) {
                    echo json_encode(["status" => "error", "message" => "Faltan datos requeridos."]);
                    return;
                }

                try {
                    $idcliente = $inputData['idcliente'];
                    $tipocom = $inputData['tipocom'];
                    $fechahora = $inputData['fechahora'];
                    $numserie = $inputData['numserie'];
                    $numcom = $inputData['numcom'];
                    $moneda = $inputData['moneda'];
                    $detalle = $inputData['detalle'];

                    $venta->registrarVenta($idcliente, $tipocom, $fechahora, $numserie, $numcom, $moneda, $detalle);

                    echo json_encode(["status" => "success", "message" => "Venta registrada correctamente."]);
                } catch (Exception $e) {
                    
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            } else {
                // Si no se ha especificado la acción
                echo json_encode(["status" => "error", "message" => "Acción no reconocida"]);
            }
            break;

    }
}
?>
