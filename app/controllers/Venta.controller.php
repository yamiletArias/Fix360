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
            // Captura el JSON de entrada
            $input = file_get_contents('php://input');
            error_log("Entrada POST: " . $input);

            $dataJSON = json_decode($input, true);
            if (!$dataJSON) {
                error_log("Error: JSON inválido.");
                echo json_encode(["status" => "error", "message" => "JSON inválido."]);
                exit;
            }

            // Limpieza y validación de datos
            $tipocom = Helper::limpiarCadena($dataJSON['tipocom'] ?? "");
            $fechahora = Helper::limpiarCadena($dataJSON['fechahora'] ?? "");
            if (empty($fechahora)) {
                $fechahora = date("Y-m-d H:i:s");
            } elseif (strpos($fechahora, ' ') === false) {
                $fechahora .= " " . date("H:i:s");
            }
            $numserie = Helper::limpiarCadena($dataJSON['numserie'] ?? "");
            $numcom = Helper::limpiarCadena($dataJSON['numcom'] ?? "");
            $moneda = Helper::limpiarCadena($dataJSON['moneda'] ?? "");
            $idcliente = $dataJSON['idcliente'] ?? 0;
            $kilometraje = $dataJSON['kilometraje'] ?? 0;
            $idvehiculo = intval($dataJSON['idvehiculo'] ?? 0); 
            $productos = $dataJSON['productos'] ?? [];

            if (empty($productos)) {
                echo json_encode(["status" => "error", "message" => "No se enviaron productos."]);
                exit;
            }

            error_log("Datos recibidos: " . print_r($dataJSON, true));

            $venta = new Venta();
            $idVentaInsertada = $venta->registerVentas([
                "tipocom" => $tipocom,
                "fechahora" => $fechahora,
                "numserie" => $numserie,
                "numcom" => $numcom,
                "moneda" => $moneda,
                "idcliente" => $idcliente,
                "idvehiculo"=> $idvehiculo,
                "kilometraje" => $kilometraje,
                "productos" => $productos
            ]);

            if ($idVentaInsertada > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Venta registrada con exito.",
                    "idventa" => $idVentaInsertada
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo registrar la venta."]);
            }
            break;

    }
}
?>