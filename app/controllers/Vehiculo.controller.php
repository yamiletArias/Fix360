<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Vehiculo.php";
    require_once "../helpers/helper.php";

    $vehiculo = new Vehiculo();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Obtener todos
            if (isset($_GET['task']) && $_GET['task'] === 'getAll') {
                echo json_encode($vehiculo->getAll());
                exit;
            }

            // Obtener vehículos por cliente
            if (isset($_GET['task']) && $_GET['task'] === 'getVehiculoByCliente') {
                $idcliente = intval($_GET['idcliente'] ?? 0);
                echo json_encode($vehiculo->getVehiculoByCliente($idcliente));
                exit;
            }

            // Obtener órdenes por vehículo
            if (isset($_GET['task']) && $_GET['task'] === 'getOrdenesByVehiculo' && isset($_GET['idvehiculo'])) {
                $idvehiculo = intval($_GET['idvehiculo']);
                echo json_encode($vehiculo->getOrdenesByVehiculo($idvehiculo));
                exit;
            }

            // Obtener ventas por vehículo
            if (isset($_GET['task']) && $_GET['task'] === 'getVentasByVehiculo' && isset($_GET['idvehiculo'])) {
                $idvehiculo = intval($_GET['idvehiculo']);
                echo json_encode($vehiculo->getVentasByVehiculo($idvehiculo));
                exit;
            }

            // Obtener último kilometraje
            if (isset($_GET['task']) && $_GET['task'] === 'getUltimoKilometraje' && isset($_GET['idvehiculo'])) {
                $idvehiculo = intval($_GET['idvehiculo']);
                echo json_encode($vehiculo->getUltimoKilometraje($idvehiculo));
                exit;
            }

            // Obtener detalle de orden de servicio
            if (isset($_GET['task']) && $_GET['task'] === 'getDetalleOrdenServicio' && isset($_GET['idorden'])) {
                $idorden = intval($_GET['idorden']);
                echo json_encode($vehiculo->getDetalleOrdenServicio($idorden));
                exit;
            }

            // Obtener justificación de orden
            if (isset($_GET['task']) && $_GET['task'] === 'getJustificacionByOrden' && isset($_GET['idorden'])) {
                $idorden = intval($_GET['idorden']);
                echo json_encode($vehiculo->getJustificacionByOrden($idorden));
                exit;
            }

            // ——— NUEVO ENDPOINT: Obtener vehículo con propietario activo ———
            if (isset($_GET['task']) && $_GET['task'] === 'getVehiculoConPropietario' && isset($_GET['idvehiculo'])) {
                $idvehiculo = intval($_GET['idvehiculo']);
                echo json_encode($vehiculo->getVehiculoConPropietario($idvehiculo));
                exit;
            }

            // Default GET inválido
            echo json_encode(['status' => false, 'message' => 'Parámetros GET inválidos']);
            exit;

        case 'POST':
            $input = file_get_contents('php://input');
            $dataJSON = json_decode($input, true);
            error_log("📥 POST raw body: " . $input);
error_log("📥 POST parsed JSON: " . print_r($dataJSON, true));

            if ($dataJSON === null) {
                echo json_encode(["error" => "JSON inválido"]);
                error_log("JSON Recibido: " . $input);
                exit;
            }

            // Registro de vehículo
            if (isset($dataJSON['task']) && $dataJSON['task'] === 'registerVehiculo') {
                $registro = [
                    "idmodelo"        => Helper::limpiarCadena($dataJSON["idmodelo"] ?? ""),
                    "idtcombustible"  => Helper::limpiarCadena($dataJSON["idtcombustible"] ?? ""),
                    "placa"           => Helper::limpiarCadena($dataJSON["placa"] ?? ""),
                    "anio"            => Helper::limpiarCadena($dataJSON["anio"] ?? ""),
                    "numserie"        => Helper::limpiarCadena($dataJSON["numserie"] ?? ""),
                    "color"           => Helper::limpiarCadena($dataJSON["color"] ?? ""),
                    "vin"             => Helper::limpiarCadena($dataJSON["vin"] ?? ""),
                    "numchasis"       => Helper::limpiarCadena($dataJSON["numchasis"] ?? ""),
                    "idcliente"       => intval($dataJSON["idcliente"] ?? 0),
                ];

                $n = $vehiculo->registerVehiculo($registro);
                if ($n === 0) {
                    echo json_encode(["error" => "No se pudo registrar el vehículo"]);
                    error_log("JSON Recibido: " . $input);
                } else {
                    echo json_encode(["success" => "Vehículo registrado", "rows" => $n]);
                }
                exit;
            }

            // ——— NUEVO ENDPOINT: Actualizar vehículo con cambio de propietario ———
            if (isset($dataJSON['task']) && $dataJSON['task'] === 'updateVehiculoConHistorico') {
                $params = [
                    "idvehiculo"        => intval($dataJSON["idvehiculo"] ?? 0),
                    "idmodelo"          => intval($dataJSON["idmodelo"] ?? 0),
                    "idtcombustible"    => intval($dataJSON["idtcombustible"] ?? 0),
                    "placa"             => Helper::limpiarCadena($dataJSON["placa"] ?? ""),
                    "anio"              => Helper::limpiarCadena($dataJSON["anio"] ?? ""),
                    "numserie"          => Helper::limpiarCadena($dataJSON["numserie"] ?? ""),
                    "color"             => Helper::limpiarCadena($dataJSON["color"] ?? ""),
                    "vin"               => Helper::limpiarCadena($dataJSON["vin"] ?? ""),
                    "numchasis"         => Helper::limpiarCadena($dataJSON["numchasis"] ?? ""),
                    "idcliente_nuevo"   => intval($dataJSON["idcliente_nuevo"] ?? 0),
                ];

                $result = $vehiculo->updateVehiculoConHistorico($params);
                if (empty($result)) {
                    echo json_encode(["status" => false, "message" => "No se pudo actualizar vehículo"]);
                } else {
                    echo json_encode([
                        "status"  => true,
                        "message" => "Vehículo actualizado con histórico",
                        "data"    => $result
                    ]);
                }
                exit;
            }

            // Si no coincide ningún task en POST
            echo json_encode(["status" => false, "message" => "Task POST no reconocido"]);
            exit;

        default:
            echo json_encode(['status' => false, 'message' => 'Método no permitido']);
            exit;
    }
}
?>
