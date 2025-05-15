<?php
// app/controllers/movimientos.controller.php
header('Content-Type: application/json; charset=utf-8');
require_once '../models/Movimiento.php';
require_once "../helpers/helper.php";  // si necesitas limpiar cadenas, etc.
session_start();

$movModel = new Movimiento();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ¿Solicita stock actual o listado de movimientos?
    $action = $_GET['action'] ?? 'listar';

    // VALIDACIÓN BÁSICA DEL ID DE PRODUCTO
    $idproducto = intval($_GET['idproducto'] ?? 0);
    if ($idproducto <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de producto inválido']);
        exit;
    }

    if ($action === 'stock') {
        // Obtener stock actual
        $stock = $movModel->obtenerStockActual($idproducto);
        echo json_encode([
            'status'       => 'success',
            'idproducto'   => $idproducto,
            'stock_actual' => $stock  // puede ser null si no existe
        ]);
        exit;
    }

    // Por defecto: listar movimientos por periodo
    $modo  = strtolower($_GET['modo']  ?? 'dia');
    $fecha = $_GET['fecha'] ?? date('Y-m-d');

    // Validar modo
    if (!in_array($modo, ['dia','semana','mes'], true)) {
        echo json_encode(['status'=>'error','message'=>'Modo inválido, use dia|semana|mes']);
        exit;
    }
    // Validar formato de fecha (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode(['status'=>'error','message'=>'Fecha inválida, formato YYYY-MM-DD']);
        exit;
    }

    $movs = $movModel->listarMovimientosPorPeriodo($idproducto, $modo, $fecha);
    echo json_encode([
        'status'     => 'success',
        'idproducto' => $idproducto,
        'modo'       => $modo,
        'fecha'      => $fecha,
        'data'       => $movs
    ]);
    exit;

} else {
    // No soportamos POST/PUT/DELETE aquí
    http_response_code(405);
    echo json_encode(['status'=>'error','message'=>'Método no permitido']);
    exit;
}
