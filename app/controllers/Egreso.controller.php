<?php
// app/controllers/egresos.controller.php
header('Content-Type: application/json; charset=utf-8');
require_once '../models/Egreso.php';
require_once '../helpers/helper.php';
session_start();

$egresoModel = new Egreso();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
        exit;
    }

    // Ruta para eliminar egreso
    if (isset($data['action']) && $data['action'] === 'delete') {
        $idegreso = intval($data['idegreso'] ?? 0);
        $justi    = Helper::limpiarCadena($data['justificacion'] ?? '');
        if ($idegreso <= 0 || $justi === '') {
            echo json_encode(['status' => 'error', 'message' => 'Parámetros inválidos']);
            exit;
        }
        $affected = $egresoModel->deleteEgreso($idegreso, $justi);
        echo json_encode($affected > 0
            ? ['status' => 'success', 'updated' => $affected]
            : ['status' => 'error', 'message' => 'No se eliminó el egreso']
        );
        exit;
    }

    // Registrar nuevo egreso
    $params = [
        'idadmin'        => $_SESSION['user_id'] ?? 1,
        'idcolaborador'  => intval($data['idcolaborador'] ?? 0),
        'idformapago'    => intval($data['idformapago'] ?? 0),
        'concepto'       => Helper::limpiarCadena($data['concepto'] ?? ''),
        'monto'          => floatval($data['monto'] ?? 0),
        'numcomprobante' => Helper::limpiarCadena($data['numcomprobante'] ?? '')
    ];

    $idegreso = $egresoModel->registerEgreso($params);
    echo json_encode($idegreso > 0
        ? ['status' => 'success', 'idegreso' => $idegreso]
        : ['status' => 'error', 'message' => 'No se pudo registrar el egreso']
    );
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Listar egresos por periodo
    $modo  = $_GET['modo']  ?? 'dia';
    $fecha = $_GET['fecha'] ?? date('Y-m-d');
    if (!in_array($modo, ['dia', 'semana', 'mes'], true)) {
        $modo = 'dia';
    }
    $egresos = $egresoModel->listarPorPeriodo($modo, $fecha);
    echo json_encode(['status' => 'success', 'data' => $egresos]);
    exit;
}