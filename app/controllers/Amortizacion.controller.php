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
  // POST: creación de amortización para venta o compra
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monto = isset($_POST['monto']) ? (float) $_POST['monto'] : 0;
    $fp = isset($_POST['idformapago']) ? (int) $_POST['idformapago'] : 0;

    if ($monto <= 0 || $fp <= 0) {
      throw new Exception('Parámetros inválidos', 400);
    }

    // Determinar tipo: venta o compra
    if (isset($_POST['idventa'])) {
      $tipo = 'venta';
      $id = (int) $_POST['idventa'];
    } elseif (isset($_POST['idcompra'])) {
      $tipo = 'compra';
      $id = (int) $_POST['idcompra'];
    } else {
      throw new Exception('idventa o idcompra faltante', 400);
    }

    // crear amortización según tipo
    $nuevo = $am->create($tipo, $id, $fp, $monto);

    echo json_encode([
      'status' => 'success',
      'message' => 'Amortización registrada',
      'amortizacion' => $nuevo
    ]);
    exit;
  }

  // GET: listar amortizaciones y totales
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['idventa'])) {
      $tipo = 'venta';
      $id = (int) $_GET['idventa'];
    } elseif (isset($_GET['idcompra'])) {
      $tipo = 'compra';
      $id = (int) $_GET['idcompra'];
    } else {
      throw new Exception('idventa o idcompra faltante', 400);
    }
    if ($id <= 0) {
      throw new Exception('ID no válido', 400);
    }

    // info de totales y lista
    $info = $am->obtenerInfo($tipo, $id);
    $data = $am->listBy($tipo, $id);

    echo json_encode([
      'status' => 'success',
      'total_original' => (float) $info['total_original'],
      'total_pagado' => (float) $info['total_pagado'],
      'total_pendiente' => (float) $info['total_pendiente'],
      'data' => $data
    ]);
    exit;
  }

  throw new Exception('Método no permitido', 405);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'status' => 'error',
    'message' => 'Error de base de datos',
    'detail' => $e->getMessage()
  ]);
  exit;
} catch (Exception $e) {
  $code = $e->getCode() ?: 500;
  http_response_code($code);
  echo json_encode([
    'status' => 'error',
    'message' => $e->getMessage(),
    'detail' => $e->getMessage()
  ]);
  exit;
}
