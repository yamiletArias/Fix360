<?php
// app/controllers/cotizacion.controller.php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/sesion.php';
require_once __DIR__ . '/../models/Cotizacion.php';
require_once __DIR__ . '/../models/Vehiculo.php';
require_once __DIR__ . '/../helpers/helper.php';

$cotizacion = new Cotizacion();
$vehiculo = new Vehiculo();

// —————— ENDPOINT: lLEVAR LA COTIZACION A UNA VENTA ——————
if (
  $_SERVER['REQUEST_METHOD'] === 'GET'
  && ($_GET['action'] ?? '') === 'getSoloCliente'
  && isset($_GET['idcotizacion'])
) {
  $cab = $cotizacion->getCabeceraById((int) $_GET['idcotizacion']);
  if (!$cab) {
    echo json_encode(['error' => 'Cotización no encontrada']);
    exit;
  }

  // Vehículos del cliente
  $vehiculos = $vehiculo->getVehiculoByCliente((int) $cab['idcliente']);
  $primerVeh = $vehiculos[0] ?? null;

  if ($primerVeh && isset($primerVeh['idvehiculo'])) {
    $idv = (int) $primerVeh['idvehiculo'];

    // Datos desde vwVehiculos
    $datosVeh = $vehiculo->getDesdeVista($idv);
    $descripcion = $datosVeh
      ? sprintf(
        '%s %s %s (%s)',
        $datosVeh['tipov'] ?? '',
        $datosVeh['marca'] ?? '',
        $datosVeh['color'] ?? '',
        $datosVeh['placa'] ?? ''
      )
      : '';

    // Último kilómetro
    $kmRow = $vehiculo->getUltimoKilometraje($idv);
    $ultimoKm = isset($kmRow['ultimo_kilometraje'])
      ? (float) $kmRow['ultimo_kilometraje']
      : 0;
  } else {
    $idv = null;
    $descripcion = '';
    $ultimoKm = 0;
  }

  echo json_encode([
    'idcliente' => $cab['idcliente'],
    'cliente' => $cab['cliente'],
    'idvehiculo' => $idv,
    'vehiculo' => $descripcion,
    'ultimo_km' => $ultimoKm,
  ]);
  exit;
}
// —————————————————————————————————————————————————

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $tipo = Helper::limpiarCadena($_GET['type'] ?? "");

    // 1) Listar por periodo si vienen modo+fecha
    if (isset($_GET['modo'], $_GET['fecha'])) {
      $modo = in_array($_GET['modo'], ['dia', 'semana', 'mes'], true)
        ? $_GET['modo']
        : 'dia';
      $fecha = $_GET['fecha'] ?: date('Y-m-d');
      $cotizaciones = $cotizacion->listarPorPeriodoCotizacion($modo, $fecha);
      echo json_encode(['status' => 'success', 'data' => $cotizaciones]);
      exit;
    }

    // 2) Cotizaciones eliminadas
    if (isset($_GET['action']) && $_GET['action'] === 'cotizaciones_eliminadas') {
      $eliminadas = $cotizacion->getCotizacionesEliminadas();
      echo json_encode(['status' => 'success', 'data' => $eliminadas]);
      exit;
    }

    // 3) Justificación de eliminación
    if (
      isset($_GET['action'], $_GET['idcotizacion'])
      && $_GET['action'] === 'justificacion'
    ) {
      $just = $cotizacion->getJustificacion((int) $_GET['idcotizacion']);
      if ($just !== null) {
        echo json_encode(['status' => 'success', 'justificacion' => $just]);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'No existe justificación']);
      }
      exit;
    }

    // 4) Monedas
    if ($tipo === 'moneda') {
      $monedas = $cotizacion->getMonedasVentas();
      echo json_encode(['status' => 'success', 'data' => $monedas]);
      exit;
    }

    // 5) Búsqueda dinámica de cliente/producto
    if (isset($_GET['q']) && $_GET['q'] !== '') {
      $termino = Helper::limpiarCadena($_GET['q']);
      $tipo    = $_GET['type'] ?? '';        // ← Asegúrate de esto
      if ($tipo === 'producto') {
        $productos = $cotizacion->buscarProducto($termino);
        // uniformiza tu formato JSON con status/data
        echo json_encode(['status' => 'success', 'data' => $productos]);
        exit;
      } else {
        $clientes = $cotizacion->buscarCliente($termino);
        echo json_encode(['status' => 'success', 'data' => $clientes]);
        exit;
      }
    }

    if (
      isset($_GET['action'], $_GET['idcotizacion'])
      && $_GET['action'] === 'detalle'
    ) {
      $id = (int) $_GET['idcotizacion'];
      $fila = $cotizacion->getCabeceraById($id);
      if ($fila) {
        echo json_encode([
          'status' => 'success',
          'data' => [
            'cliente' => $fila['cliente'] ?? null,
            'fechahora' => $fila['fechahora'] ?? null,
            'vigenciadias' => $fila['vigenciadias'] ?? null,
            'estado' => $fila['estado'] ?? null
          ]
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'No existe cotización'
        ]);
      }
      exit;
    }

    // 6) Fallback: todas las cotizaciones activas
    echo json_encode(['status' => 'success', 'data' => $cotizacion->getAll()]);
    exit;

  case 'POST':

    // → Anulación de cotización (soft-delete) con justificación
    if (
      isset($_POST['action'], $_POST['idcotizacion']) 
      && $_POST['action'] === 'eliminar'
    ) {
      $id = intval($_POST['idcotizacion']);
      $justificacion = trim($_POST['justificacion'] ?? '');
      $ok = $cotizacion->deleteCotizacion($id, $justificacion);
      echo json_encode([
        'status'  => $ok ? 'success' : 'error',
        'message' => $ok 
          ? 'Cotización anulada.' 
          : 'No se pudo anular la cotización.'
      ]);
      exit;
    }

    // → Validar que recibimos JSON
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') === false) {
      error_log("ERROR: Content-Type inválido: {$contentType}");
      echo json_encode([
        'status'  => 'error',
        'message' => 'Se esperaba application/json'
      ]);
      exit;
    }

    // → Leer el raw body UNA sola vez
    $input = file_get_contents('php://input');
    error_log("RAW POST INPUT: {$input}");

    $dataJSON = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      error_log("JSON inválido: " . json_last_error_msg());
      echo json_encode([
        'status'  => 'error',
        'message' => 'JSON inválido.'
      ]);
      exit;
    }

    // → Extraer y sanitizar campos
    $idadmin       = $_SESSION['login']['idcolaborador'] ?? 0;
    $fechahora     = Helper::limpiarCadena($dataJSON['fechahora'] ?? '');
    $vigenciaInput = Helper::limpiarCadena($dataJSON['vigenciadias'] ?? '');
    $moneda        = Helper::limpiarCadena($dataJSON['moneda'] ?? '');
    $idcliente     = intval($dataJSON['idcliente'] ?? 0);
    $items         = $dataJSON['items'] ?? [];

    // → Validar items
    if (!is_array($items) || count($items) === 0) {
      error_log("No se recibieron items o no es array: " . var_export($items, true));
      echo json_encode([
        'status'  => 'error',
        'message' => 'No se enviaron ítems de cotización.'
      ]);
      exit;
    }

    // → Normalizar fecha/hora
    if (empty($fechahora)) {
      $fechahora = date('Y-m-d H:i:s');
    } elseif (strpos($fechahora, ' ') === false) {
      $fechahora .= ' ' . date('H:i:s');
    }

    // → Calcular vigencia en días
    if (strpos($vigenciaInput, '-') !== false) {
      try {
        $f2 = new DateTime($vigenciaInput);
        $f1 = new DateTime($fechahora);
        $vigenciadias = $f1->diff($f2)->days;
      } catch (Exception $e) {
        error_log("Error al parsear vigencia: " . $e->getMessage());
        $vigenciadias = 0;
      }
    } else {
      $vigenciadias = intval($vigenciaInput);
    }

    // → Registrar cotización y detalle
    $idCotInsertada = $cotizacion->registerCotizacion([
      'fechahora'     => $fechahora,
      'vigenciadias'  => $vigenciadias,
      'moneda'        => $moneda,
      'idcolaborador' => $idadmin,
      'idcliente'     => $idcliente,
      'items'         => $items
    ]);

    if ($idCotInsertada > 0) {
      echo json_encode([
        'status'       => 'success',
        'message'      => 'Cotización registrada con éxito.',
        'idcotizacion' => $idCotInsertada
      ]);
    } else {
      error_log("Fallo registerCotizacion, devolvió 0");
      echo json_encode([
        'status'  => 'error',
        'message' => 'No se pudo registrar la cotización.'
      ]);
    }
    exit;
}
