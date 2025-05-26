<?php
// app/controllers/egresos.controller.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/sesion.php';
require_once '../models/Egreso.php';
require_once '../helpers/helper.php';

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
        'idadmin'        => $idadmin,
        'idcolaborador'  => intval($data['idcolaborador'] ?? 0),
        'idformapago'    => intval($data['idformapago'] ?? 0),
        'concepto'       => Helper::limpiarCadena($data['concepto'] ?? ''),
        'monto'          => floatval($data['monto'] ?? 0),
        'fecharegistro' => Helper::limpiarCadena($data['fecharegistro'] ?? ''),
        'numcomprobante' => Helper::limpiarCadena($data['numcomprobante'] ?? '')
    ];

    $idegreso = $egresoModel->registerEgreso($params);
    echo json_encode($idegreso > 0
        ? ['status' => 'success', 'idegreso' => $idegreso]
        : ['status' => 'error', 'message' => 'No se pudo registrar el egreso']
    );
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Listar egresos por periodo y estado
    $modo   = $_GET['modo']   ?? 'dia';
    $fecha  = $_GET['fecha']  ?? date('Y-m-d');
    $estado = $_GET['estado'] ?? 'A';  // por defecto activos

    // validación básica
    if (!in_array($modo, ['dia', 'semana', 'mes'], true)) {
        $modo = 'dia';
    }
    if (!in_array($estado, ['A', 'D'], true)) {
        $estado = 'A';
    }

    // Llamada al modelo con los 3 parámetros
    $params = [
        'modo'   => $modo,
        'fecha'  => $fecha,
        'estado' => $estado
    ];
    $egresos = $egresoModel->listarPorPeriodo($params);

    echo json_encode([
        'status' => 'success',
        'data'   => $egresos
    ]);
    exit;
}