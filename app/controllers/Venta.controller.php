<?php

if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set("America/Lima");

    require_once '../models/Venta.php';
    require_once "../helpers/helper.php";
    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $tipo = Helper::limpiarCadena($_GET['type'] ?? "");
    
            // 1) Monedas
            if ($tipo == 'moneda') {
                echo json_encode($venta->getMonedasVentas());
                exit;
            }
    
            // 2) Productos o Clientes
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                $termino = $_GET['q'];
                if ($tipo == 'producto') {
                    echo json_encode($venta->buscarProducto($termino));
                    exit;
                } else {
                    echo json_encode($venta->buscarCliente($termino));
                    exit;
                }
            }
    
            // 3) Listado de ventas por periodo
            if (isset($_GET['modo'], $_GET['fecha'])) {
                $modo = in_array($_GET['modo'], ['dia', 'semana', 'mes'], true)
                    ? $_GET['modo']
                    : 'dia';
                $fecha = $_GET['fecha'] ?: date('Y-m-d');
    
                $ventas = $venta->listarPorPeriodoVentas($modo, $fecha);
                echo json_encode(['status' => 'success', 'data' => $ventas]);
                exit;
            }
    
            // 4) Ventas eliminadas
            if (isset($_GET['action']) && $_GET['action'] === 'ventas_eliminadas') {
                $eliminadas = $venta->getVentasEliminadas();
                echo json_encode(['status' => 'success', 'data' => $eliminadas]);
                exit;
            }
    
            // 5) Justificación de eliminación
            if (
                isset($_GET['action'], $_GET['idventa'])
                && $_GET['action'] === 'justificacion'
            ) {
                $id = (int) $_GET['idventa'];
                try {
                    $just = $venta->getJustificacion($id);
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
            echo json_encode(['status' => 'success', 'data' => $venta->getAll()]);
            exit;
        case 'POST':

            // Anulación de venta (soft-delete) con justificación
            if (isset($_POST['action'], $_POST['idventa']) && $_POST['action'] === 'eliminar') {
                $id = intval($_POST['idventa']);
                $justificacion = trim($_POST['justificacion'] ?? "");

                error_log("Intentando anular compra #$id. Justificación: $justificacion");

                $ok = $venta->deleteVenta($id, $justificacion);
                error_log("Resultado deleteVenta: " . ($ok ? 'OK' : 'FAIL'));

                echo json_encode([
                    'status' => $ok ? 'success' : 'error',
                    'message' => $ok ? 'Compra anulada.' : 'No se pudo anular la compra.'
                ]);
                exit;
            }

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
            } else {
                $fecha = explode(" ", $fechahora)[0];
                $fechahora = $fecha . " " . date("H:i:s");
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
                "idvehiculo" => $idvehiculo,
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