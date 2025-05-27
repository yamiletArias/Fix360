<?php

header('Content-type: application/json; charset=utf-8');
require_once "../models/Vehiculo.php";
require_once "../helpers/helper.php";

$vehiculo = new Vehiculo();
$method   = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $task = $_GET['task'] ?? '';

    // Obtener todos los vehículos
    if ($task === 'getAll') {
        echo json_encode($vehiculo->getAll());
        exit;
    }

    // Obtener vehículos por cliente
    if ($task === 'getVehiculoByCliente') {
        $idcliente = intval($_GET['idcliente'] ?? 0);
        echo json_encode($vehiculo->getVehiculoByCliente($idcliente));
        exit;
    }

    // Obtener órdenes por vehículo
    if ($task === 'getOrdenesByVehiculo') {
        $idvehiculo = intval($_GET['idvehiculo'] ?? 0);
        echo json_encode($vehiculo->getOrdenesByVehiculo($idvehiculo));
        exit;
    }

    // Obtener ventas por vehículo
    if ($task === 'getVentasByVehiculo') {
        $idvehiculo = intval($_GET['idvehiculo'] ?? 0);
        echo json_encode($vehiculo->getVentasByVehiculo($idvehiculo));
        exit;
    }

    // Obtener último kilometraje
    if ($task === 'getUltimoKilometraje') {
        $idvehiculo = intval($_GET['idvehiculo'] ?? 0);
        echo json_encode($vehiculo->getUltimoKilometraje($idvehiculo));
        exit;
    }

    // Obtener detalle de orden de servicio
    if ($task === 'getDetalleOrdenServicio') {
        $idorden = intval($_GET['idorden'] ?? 0);
        echo json_encode($vehiculo->getDetalleOrdenServicio($idorden));
        exit;
    }

    // Obtener justificación de orden
    if ($task === 'getJustificacionByOrden') {
        $idorden = intval($_GET['idorden'] ?? 0);
        echo json_encode($vehiculo->getJustificacionByOrden($idorden));
        exit;
    }

    // Obtener vehículo con propietario activo
    if ($task === 'getVehiculoConPropietario') {
        $idvehiculo = intval($_GET['idvehiculo'] ?? 0);
        echo json_encode($vehiculo->getVehiculoConPropietario($idvehiculo));
        exit;
    }

    // Historial completo: datos generales + propietario
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

        $general = [
            'idvehiculo'    => $datos['idvehiculo'],        
            'placa'         => $datos['placa'],
            'anio'          => $datos['anio'],
            'color'         => $datos['color'],
            'numserie'      => $datos['numserie'],
            'vin'           => $datos['vin'],
            'tipo_vehiculo' => $datos['tipo_vehiculo'],  
            'tcombustible'  => $datos['tcombustible'],   
            'marca'         => $datos['marca'],
            'modelo'        => $datos['modelo'],
        ];

        // Ajuste: usar alias 'id_propietario' provisto por el SP
        $propietario = [
            'id_propietario'       => $datos['id_propietario'],
            'propietario'          => $datos['propietario'],
            'documento_propietario'=> $datos['documento_propietario'],
            'propiedad_desde'      => $datos['propiedad_desde'],   
            'propiedad_hasta'      => $datos['propiedad_hasta'],   
        ];

        echo json_encode([ 'general' => $general, 'propietario' => $propietario ]);
        exit;
    }
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

        $general = [
            'idvehiculo'    => $datos['idvehiculo'],        
            'placa'         => $datos['placa'],
            'anio'          => $datos['anio'],
            'color'         => $datos['color'],
            'numserie'      => $datos['numserie'],
            'vin'           => $datos['vin'],
            'tipo_vehiculo' => $datos['tipo_vehiculo'],  
            'tcombustible'  => $datos['tcombustible'],   
            'marca'         => $datos['marca'],
            'modelo'        => $datos['modelo'],
            // 'modificado'  => $datos['modificado'],
        ];

        $propietario = [
            'id_propietario'       => $datos['idcliente'],
            'propietario'          => $datos['propietario'],
            'documento_propietario'=> $datos['documento_propietario'],
            'propiedad_desde'      => $datos['propiedad_desde'],   
            'propiedad_hasta'      => $datos['propiedad_hasta'],   
        ];

        echo json_encode([ 'general' => $general, 'propietario' => $propietario ]);
        exit;
    }

    // Si no coincide ningún task válido
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros GET inválidos']);
    exit;
}

if ($method === 'POST') {
    $input    = file_get_contents('php://input');
    $dataJSON = json_decode($input, true);

    if ($dataJSON === null) {
        echo json_encode(['error' => 'JSON inválido']);
        exit;
    }

    // Registro de vehículo
    if (isset($dataJSON['task']) && $dataJSON['task'] === 'registerVehiculo') {
        $registro = [
            'idmodelo'       => Helper::limpiarCadena($dataJSON['idmodelo'] ?? ''),
            'idtcombustible' => Helper::limpiarCadena($dataJSON['idtcombustible'] ?? ''),
            'placa'          => Helper::limpiarCadena($dataJSON['placa'] ?? ''),
            'anio'           => Helper::limpiarCadena($dataJSON['anio'] ?? ''),
            'numserie'       => Helper::limpiarCadena($dataJSON['numserie'] ?? ''),
            'color'          => Helper::limpiarCadena($dataJSON['color'] ?? ''),
            'vin'            => Helper::limpiarCadena($dataJSON['vin'] ?? ''),
            'numchasis'      => Helper::limpiarCadena($dataJSON['numchasis'] ?? ''),
            'idcliente'      => intval($dataJSON['idcliente'] ?? 0),
        ];
        $n = $vehiculo->registerVehiculo($registro);
        if ($n === 0) {
            echo json_encode(['error' => 'No se pudo registrar el vehículo']);
        } else {
            echo json_encode(['success' => 'Vehículo registrado', 'rows' => $n]);
        }
        exit;
    }

    // Actualizar vehículo con histórico de propietarios
    if (isset($dataJSON['task']) && $dataJSON['task'] === 'updateVehiculoConHistorico') {
        $params = [
            'idvehiculo'      => intval($dataJSON['idvehiculo'] ?? 0),
            'idmodelo'        => intval($dataJSON['idmodelo'] ?? 0),
            'idtcombustible'  => intval($dataJSON['idtcombustible'] ?? 0),
            'placa'           => Helper::limpiarCadena($dataJSON['placa'] ?? ''),
            'anio'            => Helper::limpiarCadena($dataJSON['anio'] ?? ''),
            'numserie'        => Helper::limpiarCadena($dataJSON['numserie'] ?? ''),
            'color'           => Helper::limpiarCadena($dataJSON['color'] ?? ''),
            'vin'             => Helper::limpiarCadena($dataJSON['vin'] ?? ''),
            'numchasis'       => Helper::limpiarCadena($dataJSON['numchasis'] ?? ''),
            'idcliente_nuevo' => intval($dataJSON['idcliente_nuevo'] ?? 0),
        ];
        $result = $vehiculo->updateVehiculoConHistorico($params);
        if (empty($result)) {
            echo json_encode(['status' => false, 'message' => 'No se pudo actualizar vehículo']);
        } else {
            echo json_encode(['status' => true, 'message' => 'Vehículo actualizado con histórico', 'data' => $result]);
        }
        exit;
    }

    // Task POST no reconocido
    http_response_code(400);
    echo json_encode(['error' => 'Task POST no reconocido']);
    exit;
}

// Método no permitido
http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
exit;
?>
