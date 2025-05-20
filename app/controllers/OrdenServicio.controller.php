<?php
// app/controllers/ordenservicio.controller.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/sesion.php';
require_once __DIR__ . '/../models/OrdenServicio.php';
require_once __DIR__ . '/../helpers/helper.php';

$ordenModel = new OrdenServicio();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer y decodificar JSON entrante
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
        exit;
    }

    // 1) Ruta para “setFechaSalida”
    if (isset($data['action']) && $data['action'] === 'setSalida') {
        $idorden = intval($data['idorden'] ?? 0);
        if ($idorden <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID de orden inválido']);
            exit;
        }
        $affected = $ordenModel->setFechaSalida($idorden);
        if ($affected > 0) {
            echo json_encode(['status' => 'success', 'updated' => $affected]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se actualizó fecha de salida']);
        }
        exit;
    }

    // 2) Ruta para “eliminar” (soft-delete) una orden
    if (isset($data['action']) && $data['action'] === 'eliminar') {
        $idorden       = intval($data['idorden'] ?? 0);
        $justificacion = trim($data['justificacion'] ?? '');
        if ($idorden <= 0) {
            echo json_encode(['status'=>'error','message'=>'ID de orden inválido']);
            exit;
        }
        if ($justificacion === '') {
            echo json_encode(['status'=>'error','message'=>'Se requiere justificación']);
            exit;
        }
        $affected = $ordenModel->deleteOrdenServicio($idorden, $justificacion);
        if ($affected > 0) {
            echo json_encode(['status'=>'success','deleted'=> $affected]);
        } else {
            echo json_encode(['status'=>'error','message'=>'No se pudo eliminar la orden']);
        }
        exit;
    }

    // 3) Ruta para registrar nueva orden
    $params = [
        'idadmin'           => $idadmin,
        'idpropietario'     => intval($data['idpropietario']),
        'idcliente'         => intval($data['idcliente']),
        'idvehiculo'        => intval($data['idvehiculo']),
        'kilometraje'       => floatval($data['kilometraje']),
        'observaciones'     => Helper::limpiarCadena($data['observaciones'] ?? ''),
        'ingresogrua'       => boolval($data['ingresogrua'] ?? false),
        'fechaingreso'      => Helper::limpiarCadena($data['fechaingreso']),
        'servicios'         => array_map(function($item) {
            return [
                'idservicio' => intval($item['idservicio']),
                'idmecanico' => intval($item['idmecanico']),
                'precio'     => floatval($item['precio'])
            ];
        }, $data['detalle'] ?? [])
    ];

    $idorden = $ordenModel->registerOrdenServicio($params);

    if ($idorden > 0) {
        echo json_encode(['status' => 'success', 'idorden' => $idorden]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo registrar la orden']);
    }
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 1) Detalle para modal
    if (isset($_GET['action']) && $_GET['action'] === 'getDetalle') {
        $idorden = intval($_GET['idorden'] ?? 0);
        if ($idorden <= 0) {
            echo json_encode(['status'=>'error','message'=>'ID inválido']);
            exit;
        }
        $data = $ordenModel->getDetalleOrden($idorden);
        echo json_encode(['status'=>'success','data'=>$data]);
        exit;
    }

    // 2) Listado de órdenes por periodo y estado
    $modo   = $_GET['modo']   ?? 'dia';
    $fecha  = $_GET['fecha']  ?? date('Y-m-d');
    $estado = $_GET['estado'] ?? 'A';

    if (!in_array($modo, ['dia','semana','mes'], true)) {
        $modo = 'dia';
    }
    if (!in_array($estado, ['A','D'], true)) {
        $estado = 'A';
    }

    $ordenes = $ordenModel->listarPorPeriodo($modo, $fecha, $estado);
    echo json_encode([
        'status' => 'success',
        'modo'   => $modo,
        'fecha'  => $fecha,
        'estado' => $estado,
        'data'   => $ordenes
    ]);
    exit;
}
