<?php

if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');

    require_once '../models/Compra.php';
    require_once "../helpers/helper.php";
    $compra = new Compra();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $tipo = Helper::limpiarCadena($_GET['type'] ?? "");

            // Si el tipo es 'proveedor', obtener los proveedores
            if ($tipo === 'proveedor') {
                echo json_encode($compra->getProveedoresCompra());
            }

            elseif (isset($_GET['q']) && !empty($_GET['q'])) {
                $termino = Helper::limpiarCadena($_GET['q']);
                if ($tipo === 'producto') {
                    echo json_encode($compra->buscarProductoCompra($termino));
                } else {
                    echo json_encode(["error" => "Tipo no válido para búsqueda"]);
                }
            }

            else {
                echo json_encode($compra->getAll());
            }

            break;

        case 'POST':
            $input = file_get_contents('php://input');
            error_log("Entrada POST (compras): " . $input);

            $dataJSON = json_decode($input, true);

            if (!$dataJSON) {
                error_log("Error: JSON invalido en compras.");
                echo json_encode(["status" => "error", "message" => "JSON invalido."]);
                exit;
            }

            // Validación de datos
            $fechacompra = Helper::limpiarCadena($dataJSON['fechacompra'] ?? "");
            if (empty($fechacompra)) {
                $fechacompra = date("Y-m-d");
            }

            $tipocom = Helper::limpiarCadena($dataJSON['tipocom'] ?? "");
            $numserie = Helper::limpiarCadena($dataJSON['numserie'] ?? "");
            $numcom = Helper::limpiarCadena($dataJSON['numcom'] ?? "");
            $moneda = Helper::limpiarCadena($dataJSON['moneda'] ?? "");
            $idproveedor = $dataJSON['idproveedor'] ?? 0;
            $productos = $dataJSON['productos'] ?? [];

            if (empty($productos)) {
                echo json_encode(["status" => "error", "message" => "No se enviaron productos."]);
                exit;
            }

            error_log("Datos recibidos para compra: " . print_r($dataJSON, true));

            // Registrar compra
            $compra = new Compra();
            $idCompraInsertada = $compra->registerCompras([
                "fechacompra" => $fechacompra,
                "tipocom" => $tipocom,
                "numserie" => $numserie,
                "numcom" => $numcom,
                "moneda" => $moneda,
                "idproveedor" => $idproveedor,
                "productos" => $productos
            ]);

            if ($idCompraInsertada > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Compra registrada con exito.",
                    "idcompra" => $idCompraInsertada
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo registrar la compra."]);
            }
            break;

    }
}
?>