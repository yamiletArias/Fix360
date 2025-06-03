<?php
header('Content-type: application/json; charset=utf-8');
require_once "../models/Vehiculo.php";
require_once "../helpers/helper.php";

$vehiculo = new Vehiculo();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $task = $_GET['task'] ?? '';

    // 1) Obtener todos los vehículos
    if ($task === 'getAll') {
        echo json_encode($vehiculo->getAll());
        exit;
    }

    // 2) Obtener vehículos por cliente
    if ($task === 'getVehiculoByCliente') {
        $idcliente = intval($_GET['idcliente'] ?? 0);
        echo json_encode($vehiculo->getVehiculoByCliente($idcliente));
        exit;
    }

    // 3) Obtener órdenes por vehículo
    if ($task === 'getOrdenesByVehiculo') {
        $idvehiculo = intval($_GET['idvehiculo'] ?? 0);
        echo json_encode($vehiculo->getOrdenesByVehiculo($idvehiculo));
        exit;
    }

    // 4) Obtener ventas por vehículo
    if ($task === 'getVentasByVehiculo') {
        $idvehiculo = intval($_GET['idvehiculo'] ?? 0);
        echo json_encode($vehiculo->getVentasByVehiculo($idvehiculo));
        exit;
    }

    // 5) Obtener último kilometraje
    if ($task === 'getUltimoKilometraje') {
        $idvehiculo = intval($_GET['idvehiculo'] ?? 0);
        echo json_encode($vehiculo->getUltimoKilometraje($idvehiculo));
        exit;
    }

    // 6) Obtener detalle de orden de servicio
    if ($task === 'getDetalleOrdenServicio') {
        $idorden = intval($_GET['idorden'] ?? 0);
        echo json_encode($vehiculo->getDetalleOrdenServicio($idorden));
        exit;
    }

    // 7) Obtener justificación de orden
    if ($task === 'getJustificacionByOrden') {
        $idorden = intval($_GET['idorden'] ?? 0);
        echo json_encode($vehiculo->getJustificacionByOrden($idorden));
        exit;
    }

    // 8) Obtener vehículo con propietario activo
    if ($task === 'getVehiculoConPropietario') {
        $idvehiculo = intval($_GET['idvehiculo'] ?? 0);
        echo json_encode($vehiculo->getVehiculoConPropietario($idvehiculo));
        exit;
    }

    // 9) Historial completo: datos generales + propietario
    if ($task === 'getHistorial') {
        $id = intval($_GET['idvehiculo'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de vehículo inválido']);
            exit;
        }

        $datos = $vehiculo->getDatosGeneralesVehiculo($id);
        if (!$datos) {
            http_response_code(404);
            echo json_encode(['error' => 'Vehículo no encontrado']);
            exit;
        }

        // Armar la sección "general"
        $general = [
            'idvehiculo' => $datos['idvehiculo'],
            'placa' => $datos['placa'],
            'anio' => $datos['anio'],
            'color' => $datos['color'],
            'numserie' => $datos['numserie'],
            'numchasis' => $datos['numchasis'],
            'vin' => $datos['vin'],
            'modificado' => $datos['modificado'],
            'tipo_vehiculo' => $datos['tipo_vehiculo'],
            'tcombustible' => $datos['tcombustible'],
            'marca' => $datos['marca'],
            'modelo' => $datos['modelo'],
        ];

        // Armar la sección "propietario"
        $propietario = [
            'id_propietario' => $datos['id_propietario'],            // si el SP usa alias idpropietario
            'propietario' => $datos['propietario'],
            'documento_propietario' => $datos['documento_propietario'],
            'telefono_prop' => $datos['telefono_prop'] ?? null,
            'email_prop' => $datos['email_prop'] ?? null,
            'propiedad_desde' => $datos['propiedad_desde'] ?? null,
            'propiedad_hasta' => $datos['propiedad_hasta'] ?? null,
        ];

        echo json_encode([
            'status' => 'success',
            'general' => $general,
            'propietario' => $propietario
        ]);
        exit;
    }

    // Si no coincide ningún task válido
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros GET inválidos']);
    exit;
}

// ===============================
// = Manejador de peticiones POST =
// ===============================
if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $dataJSON = json_decode($input, true);

    if ($dataJSON === null) {
        http_response_code(400);
        echo json_encode(['error' => 'JSON inválido']);
        exit;
    }

    // 1) Registro de vehículo
    if (isset($dataJSON['task']) && $dataJSON['task'] === 'registerVehiculo') {
        $registro = [
            'idmodelo' => intval(Helper::limpiarCadena($dataJSON['idmodelo'] ?? '0')),
            'idtcombustible' => intval(Helper::limpiarCadena($dataJSON['idtcombustible'] ?? '0')),
            'placa' => Helper::limpiarCadena($dataJSON['placa'] ?? ''),
            'anio' => Helper::limpiarCadena($dataJSON['anio'] ?? ''),
            'numserie' => Helper::limpiarCadena($dataJSON['numserie'] ?? ''),
            'color' => Helper::limpiarCadena($dataJSON['color'] ?? ''),
            'vin' => Helper::limpiarCadena($dataJSON['vin'] ?? ''),
            'numchasis' => Helper::limpiarCadena($dataJSON['numchasis'] ?? ''),
            'idcliente' => intval($dataJSON['idcliente'] ?? 0),
        ];

        $n = $vehiculo->registerVehiculo($registro);
        if ($n === 0) {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo registrar el vehículo']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Vehículo registrado', 'rows' => $n]);
        }
        exit;
    }

    // 2) Actualizar vehículo con historial de propietarios
    if (isset($dataJSON['task']) && $dataJSON['task'] === 'updateVehiculoConHistorico') {
        $params = [
            'idvehiculo' => intval($dataJSON['idvehiculo'] ?? 0),
            'idmodelo' => intval($dataJSON['idmodelo'] ?? 0),
            'idtcombustible' => intval($dataJSON['idtcombustible'] ?? 0),
            'placa' => Helper::limpiarCadena($dataJSON['placa'] ?? ''),
            'anio' => Helper::limpiarCadena($dataJSON['anio'] ?? ''),
            'numserie' => Helper::limpiarCadena($dataJSON['numserie'] ?? ''),
            'color' => Helper::limpiarCadena($dataJSON['color'] ?? ''),
            'vin' => Helper::limpiarCadena($dataJSON['vin'] ?? ''),
            'numchasis' => Helper::limpiarCadena($dataJSON['numchasis'] ?? ''),
            'idcliente_nuevo' => intval($dataJSON['idcliente_nuevo'] ?? 0),
        ];

        try {
            $result = $vehiculo->updateVehiculoConHistorico($params);
            if (!is_array($result) || !array_key_exists('idcliente_propietario_nuevo', $result)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se pudo actualizar vehículo (no se devolvió idcliente)'
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Vehículo actualizado correctamente',
                    'data' => $result
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error en SP: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    // Task inválido en POST
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Parámetros POST inválidos']);
    exit;
}

// Método no permitido
http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
exit;
?>