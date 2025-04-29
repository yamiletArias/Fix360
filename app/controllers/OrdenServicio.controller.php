<?php
// app/controllers/ordenservicio.controller.php
header('Content-Type: application/json; charset=utf-8');
require_once '../models/OrdenServicio.php';
require_once "../helpers/helper.php";
session_start();

$ordenModel = new OrdenServicio();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer y decodificar JSON entrante
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
        exit;
    }

    // Preparar parámetros para el modelo
    $params = [
        'idadmin'           => $_SESSION['user_id'] ?? 0,
        'idmecanico'        => intval($data['idmecanico']),
        'idpropietario'     => intval($data['idpropietario']),
        'idcliente'         => intval($data['idcliente']),
        'idvehiculo'        => intval($data['idvehiculo']),
        'kilometraje'       => floatval($data['kilometraje']),
        'observaciones'     => Helper::limpiarCadena($data['observaciones'] ?? ''),
        'ingresogrua'       => boolval($data['ingresogrua'] ?? false),
        'fechaingreso'      => Helper::limpiarCadena($data['fechaingreso']),
        'fecharecordatorio' => Helper::limpiarCadena($data['fecharecordatorio'] ?? null),
        'detalle'           => array_map(function($item) {
            return [
                'idservicio' => intval($item['idservicio']),
                'precio'     => floatval($item['precio'])
            ];
        }, $data['detalle'] ?? [])
    ];

    // Llamada al modelo para insertar cabecera y detalle
    $idorden = $ordenModel->registerOrden($params);

    if ($idorden > 0) {
        echo json_encode(['status' => 'success', 'idorden' => $idorden]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo registrar la orden']);
    }

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
