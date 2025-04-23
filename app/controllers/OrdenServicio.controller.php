<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../models/OrdenServicio.php';
require_once "../helpers/helper.php";
session_start();

$orden = new OrdenServicio();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['status'=>'error','message'=>'JSON inválido']);
        exit;
    }

    // Obtén el admin de la sesión
    $idadmin = $_SESSION['user_id'] ?? 0;

    // Prepara parámetros
    $params = [
        'idadmin'         => $idadmin,
        'idmecanico'      => intval($data['idmecanico']),
        'idpropietario'   => intval($data['idpropietario']),
        'idcliente'       => intval($data['idcliente']),
        'idvehiculo'      => intval($data['idvehiculo']),
        'kilometraje'     => floatval($data['kilometraje']),
        'observaciones'   => Helper::limpiarCadena($data['observaciones'] ?? ''),
        'ingresogrua'     => boolval($data['ingresogrua'] ?? false),
        'fechaingreso'    => Helper::limpiarCadena($data['fechaingreso']),
        'fecharecordatorio'=> Helper::limpiarCadena($data['fecharecordatorio'] ?? null),
        'detalle'         => $data['detalle']       // array de {idservicio, precio}
    ];

    $idorden = $orden->registerOrden($params);
    if ($idorden > 0) {
        echo json_encode(['status'=>'success','idorden'=>$idorden]);
    } else {
        echo json_encode(['status'=>'error','message'=>'No se pudo registrar la orden']);
    }
}
