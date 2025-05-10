<?php
// controllers/Amortizacion.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set("America/Lima");

require_once __DIR__ . '/../models/Amortizacion.php';
$am = new Amortizacion();

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idventa'], $_POST['monto'], $_POST['idformapago'])) {
    $idv = (int) $_POST['idventa'];
    $monto = (float) $_POST['monto'];
    $fp = (int) $_POST['idformapago'];

    if ($idv <= 0 || $monto <= 0 || $fp <= 0) {
      throw new Exception('Parámetros inválidos', 400);
    }

    // usa el método create del modelo
    $nuevo = $am->create($idv, $fp, $monto);

    echo json_encode([
      'status' => 'success',
      'message' => 'Amortización registrada',
      'amortizacion' => $nuevo
    ]);
    exit;
  }
  
  if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['idventa'])) {
    throw new Exception('Método no permitido o idventa faltante', 405);
  }

  $idventa = (int) $_GET['idventa'];
  if ($idventa <= 0) {
    throw new Exception('idventa no válido', 400);
  }

  // info de la venta
  $info = $am->obtenerInfoVenta($idventa);
  // lista de amortizaciones
  $data = $am->listByVenta($idventa);

  echo json_encode([
    'status' => 'success',
    'total_venta' => (float) $info['total_venta'],
    'total_pagado' => (float) $info['total_pagado'],
    'saldo_restante' => (float) $info['saldo_restante'],
    'data' => $data
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'status' => 'error',
    'message' => 'Error de base de datos',
    'detail' => $e->getMessage()
  ]);
} catch (Exception $e) {
  http_response_code($e->getCode() ?: 500);
  echo json_encode([
    'status' => 'error',
    'message' => $e->getMessage()
  ]);
}
exit;



