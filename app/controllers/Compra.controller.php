<?php

if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');

    require_once '../models/Compra.php';
    require_once "../helpers/helper.php";

    $compra = new Compra();

    switch ($_SERVER['REQUEST_METHOD']) {

        case 'GET':
            $tipo = Helper::limpiarCadena($_GET['type'] ?? "");

            // 1) Obtener proveedores
            if ($tipo === 'proveedor') {
                echo json_encode($compra->getProveedoresCompra());
            }

            // 2) Buscar productos por término
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                $termino = Helper::limpiarCadena($_GET['q']);
                if ($tipo === 'producto') {
                    echo json_encode($compra->buscarProductoCompra($termino));
                } else {
                    echo json_encode(["error" => "Tipo no válido para búsqueda"]);
                }
            }

            // 3) Listado de compra por periodo
            if (isset($_GET['modo'], $_GET['fecha'])) {
                $modo = in_array($_GET['modo'], ['dia', 'semana', 'mes'], true)
                    ? $_GET['modo']
                    : 'dia';
                $fecha = $_GET['fecha'] ?: date('Y-m-d');
    
                $compras = $compra->listarPorPeriodoCompras($modo, $fecha);
                echo json_encode(['status' => 'success', 'data' => $compras]);
                exit;
            }

            // 4) Compras eliminadas
            if (isset($_GET['action']) && $_GET['action'] === 'compras_eliminadas') {
                $eliminadas = $compra->getComprasEliminadas();
                echo json_encode(['status' => 'success', 'data' => $eliminadas]);
                exit;
            }

            // 5) Justificación de eliminación
            if (
                isset($_GET['action'], $_GET['idcompra'])
                && $_GET['action'] === 'justificacion'
            ) {
                $id = (int) $_GET['idcompra'];
                try {
                    $just = $compra->getJustificacion($id);
                    if ($just !== null) {
                        echo json_encode(['status' => 'success', 'justificacion' => $just]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'No existe justificación']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                exit;
            }

            // 6) Listar todas las ventas si no se especifica nada
            echo json_encode(['status' => 'success', 'data' => $compra->getAll()]);
            exit;

            // Obtener todas las compras
            /* else {
                echo json_encode($compra->getAll());
            }
            break; */

        case 'POST':
            // Anulación de compra (soft-delete) con justificación
            if (isset($_POST['action'], $_POST['idcompra']) && $_POST['action'] === 'eliminar') {
                $id = intval($_POST['idcompra']);
                $justificacion = trim($_POST['justificacion'] ?? "");

                error_log("Intentando anular compra #$id. Justificación: $justificacion");

                $ok = $compra->deleteCompra($id, $justificacion);
                error_log("Resultado deleteCompra: " . ($ok ? 'OK' : 'FAIL'));

                echo json_encode([
                    'status' => $ok ? 'success' : 'error',
                    'message' => $ok ? 'Compra anulada.' : 'No se pudo anular la compra.'
                ]);
                exit;
            }

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