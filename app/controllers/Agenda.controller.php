<?php
// app/controllers/agenda.controller.php

header('Content-Type: application/json; charset=utf-8');
require_once '../models/Agenda.php';
require_once '../helpers/Helper.php';

session_start();
$agendaModel = new Agenda();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Determinar tarea
    $task = $_GET['task'] ?? '';
    switch ($task) {
        case 'getToday':
            $data = $agendaModel->getRecordatoriosHoy();
            echo json_encode(['status' => 'success', 'data' => $data]);
            break;
        case 'listByPeriod':
            // Parámetros: modo, fecha, estado
            $modo   = $_GET['modo']   ?? 'dia';
            $fecha  = $_GET['fecha']  ?? date('Y-m-d');
            $estado = $_GET['estado'] ?? 'P';
            $result = $agendaModel->ListAgendasPorPeriodo([
                'modo'   => Helper::limpiarCadena($modo),
                'fecha'  => Helper::limpiarCadena($fecha),
                'estado' => Helper::limpiarCadena($estado)
            ]);
            echo json_encode(['status' => 'success', 'data' => $result]);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Task inválida']);
    }
    exit;
}

if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $dataJSON = json_decode($input, true);
    if ($dataJSON === null) {
        echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
        error_log('AgendaController JSON inválido: '.$input);
        exit;
    }

    $action = $dataJSON['action'] ?? '';

    switch ($action) {
        case 'register':
            // Registrar nuevo recordatorio
            $params = [
                'idpropietario'  => intval($dataJSON['idpropietario'] ?? 0),
                'fchproxvisita'  => Helper::limpiarCadena($dataJSON['fchproxvisita'] ?? ''),
                'comentario'     => Helper::limpiarCadena($dataJSON['comentario']    ?? '')
            ];
            $rows = $agendaModel->RegisterRecordatorio($params);
            if ($rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Recordatorio registrado', 'rows' => $rows]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo registrar']);
            }
            break;

        case 'updateEstado':
            // Actualizar estado
            $idagenda = intval($dataJSON['idagenda'] ?? 0);
            $estado   = Helper::limpiarCadena($dataJSON['estado'] ?? '');
            $rows = $agendaModel->updateEstado($idagenda, $estado);
            if ($rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Estado actualizado', 'rows' => $rows]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se actualizó estado']);
            }
            break;

        case 'reprogramar':
            // Reprogramar y poner estado R
            $idagenda    = intval($dataJSON['idagenda'] ?? 0);
            $nuevaFecha  = Helper::limpiarCadena($dataJSON['nueva_fecha'] ?? '');
            $rows = $agendaModel->reprogramarRecordatorio($idagenda, $nuevaFecha);
            if ($rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Recordatorio reprogramado', 'rows' => $rows]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se reprogramó']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Action inválida']);
    }
    exit;
}

// Si llegamos aquí, método no soportado
http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
?>
