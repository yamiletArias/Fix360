<?php
// app/controllers/Formapago.controller.php
header('Content-Type: application/json; charset=utf-8');
require_once '../models/Formapago.php';
session_start();

$formapagoModel = new Formapago();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Listar todas las formas de pago desde la vista vwFormaPagos
    $formas = $formapagoModel->getAll();
    echo json_encode([
        'status' => 'success',
        'data'   => $formas
    ]);
    exit;
}

// Si llega por otro método, devolvemos error
echo json_encode([
    'status'  => 'error',
    'message' => 'Método no soportado'
]);
exit;
