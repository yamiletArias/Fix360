<?php
// app/controllers/ordenservicio.controller.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/sesion.php';
require_once '../models/OrdenServicio.php';
require_once "../helpers/helper.php";


$ordenModel = new OrdenServicio();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer y decodificar JSON entrante
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
        exit;
    }

    // Ruta para “setFechaSalida”
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

    // Ruta para registrar nueva orden
    $params = [
        'idadmin'           => $idadmin,
        'idpropietario'     => intval($data['idpropietario']),
        'idcliente'         => intval($data['idcliente']),
        'idvehiculo'        => intval($data['idvehiculo']),
        'kilometraje'       => floatval($data['kilometraje']),
        'observaciones'     => Helper::limpiarCadena($data['observaciones'] ?? ''),
        'ingresogrua'       => boolval($data['ingresogrua'] ?? false),
        'fechaingreso'      => Helper::limpiarCadena($data['fechaingreso']),
        'fecharecordatorio' => Helper::limpiarCadena($data['fecharecordatorio'] ?? null),
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
    // Listado de órdenes por periodo
    $modo  = $_GET['modo']  ?? 'dia';
    $fecha = $_GET['fecha'] ?? date('Y-m-d');
    if (!in_array($modo, ['dia', 'semana', 'mes'], true)) {
        $modo = 'dia';
    }

    $ordenes = $ordenModel->listarPorPeriodo($modo, $fecha);
    echo json_encode(['status' => 'success', 'data' => $ordenes]);
    exit;
}
