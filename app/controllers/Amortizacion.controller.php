<?php
// controllers/Amortizacion.controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set("America/Lima");

// 1) Autenticación
require_once __DIR__ . '/../models/sesion.php';
// — en sesion.php esperamos que hagas session_start() y validación,
//   y que dejes disponibles:
//     $_SESSION['login']['status']  === true
//     $_SESSION['login']['idcolaborador']

if (
  !isset($_SESSION['login']['status']) ||
  $_SESSION['login']['status'] !== true
) {
  http_response_code(401);
  echo json_encode([
    'status' => 'error',
    'message' => 'No autorizado'
  ]);
  exit;
}

$idadmin = $_SESSION['login']['idcolaborador'];
// Por defecto, el colaborador receptor es el mismo que el admin:
$idcolaborador = $_SESSION['login']['idcolaborador'];

require_once __DIR__ . '/../models/Amortizacion.php';
require_once __DIR__ . '/../helpers/helper.php';

$am = new Amortizacion();

try {
  // ─── POST: creación de amortización o egreso ─────────────────────────────────────
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer inputs
    $tipo = $_POST['tipo'] ?? null;           // 'venta' o 'compra'
    $idventa = isset($_POST['idventa']) ? (int) $_POST['idventa'] : null;
    $idcompra = isset($_POST['idcompra']) ? (int) $_POST['idcompra'] : null;
    $id = $idventa ?? $idcompra;
    $idformapago = (int) ($_POST['idformapago'] ?? 0);
    $monto = (float) ($_POST['monto'] ?? 0);
    $numTrans = $_POST['numtransaccion'] ?? null;
    $numcomprobante = $_POST['numcomprobante'] ?? null;
    $justificacion = $_POST['justificacion'] ?? null;

    // Validaciones básicas
    if (
      !in_array($tipo, ['venta', 'compra'], true) ||
      !$id ||
      $idformapago <= 0 ||
      $monto <= 0
    ) {
      throw new Exception('Parámetros inválidos', 400);
    }

    // Llamada al modelo
    $resultado = $am->create(
      $tipo,
      $id,
      $idformapago,
      $monto,
      $idadmin,
      $idcolaborador,
      $numTrans,
      $numcomprobante,
      $justificacion
    );

    echo json_encode([
      'status' => 'success',
      'message' => $tipo === 'venta'
        ? 'Amortización registrada'
        : 'Egreso de compra registrado',
      'data' => $resultado
    ]);
    exit;
  }

  // ─── GET: listar amortizaciones o egresos ───────────────────────────────────────
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['idventa'])) {
      // Venta → amortizaciones
      $tipo = 'venta';
      $id = (int) $_GET['idventa'];
      if ($id <= 0) {
        throw new Exception('ID de venta no válido', 400);
      }

      $info = $am->obtenerInfo($tipo, $id);
      $data = $am->listBy($tipo, $id);

      echo json_encode([
        'status' => 'success',
        'tipo' => 'venta',
        'total_original' => (float) $info['total_original'],
        'total_pagado' => (float) $info['total_pagado'],
        'total_pendiente' => (float) $info['total_pendiente'],
        'data' => $data
      ]);
      exit;

    } elseif (isset($_GET['idcompra'])) {
      $tipo = 'compra';
      $id = (int) $_GET['idcompra'];
      if ($id <= 0)
        throw new Exception('ID de compra no válido', 400);

      // 1) Sacamos totales
      $info = $am->obtenerInfo($tipo, $id);

      // 2) Sacamos egresos (los "pagos" de la compra)
      $pdo = (new Conexion())->getConexion();
      $sql = "SELECT e.idegreso,
                   e.fecharegistro AS creado,
                   f.formapago,
                   e.monto,
                   e.numcomprobante AS numtransaccion,
                   e.justificacion,
                   ( ? - SUM(e.monto) OVER (ORDER BY e.fecharegistro ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ) AS saldo
            FROM egresos e
            LEFT JOIN formapagos f ON e.idformapago = f.idformapago
            WHERE e.idcompra = ?
            ORDER BY e.fecharegistro";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$info['total_original'], $id]);
      $egresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

      echo json_encode([
        'status' => 'success',
        'tipo' => 'compra',
        'total_original' => (float) $info['total_original'],
        'total_pagado' => (float) $info['total_pagado'],
        'total_pendiente' => (float) $info['total_pendiente'],
        'data' => $egresos
      ]);
      exit;
    }

    throw new Exception('idventa o idcompra faltante', 400);
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
  http_response_code($e->getCode() ?: 500);
  echo json_encode([
    'status' => 'error',
    'message' => $e->getMessage()
  ]);
  exit;
}