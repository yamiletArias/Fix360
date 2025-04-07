<?php


if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');

    require_once '../models/Venta.php';
    require_once "../helpers/helper.php";
    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $tipo = Helper::limpiarCadena($dataJSON['tipo'] ?? "");
            if (isset($_GET['type']) && $_GET['type'] == 'moneda') {
                echo json_encode($venta->getMonedasVentas());
            } else if (isset($_GET['q']) && !empty($_GET['q'])) {
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
            $input = file_get_contents('php://input');
            $dataJSON = json_decode($input, true);

            // Limpiar y asignar datos del encabezado
            $tipocom = Helper::limpiarCadena($dataJSON['tipocom'] ?? "");
            $fechahora = Helper::limpiarCadena($dataJSON['fechahora'] ?? "");
            // Si la fecha no incluye hora, agregar " 00:00:00"
            if (strpos($fechahora, ' ') === false) {
                $fechahora .= " 00:00:00";
            }
            $numserie = Helper::limpiarCadena($dataJSON['numserie'] ?? "");
            $numcom = Helper::limpiarCadena($dataJSON['numcom'] ?? "");
            $moneda = Helper::limpiarCadena($dataJSON['moneda'] ?? "");
            $idcliente = $dataJSON['idcliente'] ?? 0;
            $productos = $dataJSON['productos'] ?? [];

            if (empty($productos)) {
                echo json_encode(["status" => "error", "message" => "No se enviaron productos."]);
                exit;
            }

            $venta = new Venta();
            $n = $venta->registerVentas([
                "tipocom" => $tipocom,
                "fechahora" => $fechahora,
                "numserie" => $numserie,
                "numcom" => $numcom,
                "moneda" => $moneda,
                "idcliente" => $idcliente,
                "productos" => $productos
            ]);

            if ($n > 0) {
                echo json_encode(["status" => "success", "message" => "Venta registrada exitosamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo registrar la venta."]);
            }
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Método no permitido."]);
            break;
    }
}
?>