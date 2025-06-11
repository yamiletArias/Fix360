<?php
// app/controllers/cotizacion.controller.php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/sesion.php';
require_once __DIR__ . '/../models/Cotizacion.php';
require_once __DIR__ . '/../models/Vehiculo.php';
require_once __DIR__ . '/../helpers/helper.php';

$cotizacion = new Cotizacion();
$vehiculo = new Vehiculo();

// —————— ENDPOINT: obtener solo cliente de la cotización ——————
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
      $term = Helper::limpiarCadena($_GET['q']);
      if ($tipo === 'producto') {
        $res = $cotizacion->buscarProducto($term);
      } else {
        $res = $cotizacion->buscarCliente($term);
      }
      echo json_encode(['status' => 'success', 'data' => $res]);
      exit;
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

    // Anulación de venta (soft-delete) con justificación
    if (isset($_POST['action'], $_POST['idcotizacion']) && $_POST['action'] === 'eliminar') {
      $id = intval($_POST['idcotizacion']);
      $justificacion = trim($_POST['justificacion'] ?? '');
      // ¡Ojo! Aquí debes usar tu método deleteCotizacion, no deleteVenta
      $ok = $cotizacion->deleteCotizacion($id, $justificacion);
      echo json_encode([
        'status' => $ok ? 'success' : 'error',
        'message' => $ok ? 'Cotización anulada.' : 'No se pudo anular la cotización.'
      ]);
      exit;
    }

    //captura el json de entrada
    $input = file_get_contents('php://input');
    error_log("Entrada POST: " . $input);

    $dataJSON = json_decode($input, true);
    if (!$dataJSON) {
      error_log("Error: JSON inválido.");
      echo json_encode(["status" => "error", "message" => "JSON inválido."]);
      exit;
    }

    //limpiar y validacion de datos
    $idadmin = $_SESSION['login']['idcolaborador'] ?? 0;
    $fechahora = Helper::limpiarCadena($dataJSON['fechahora'] ?? "");
    $vigenciaInput = Helper::limpiarCadena($dataJSON['vigenciadias'] ?? "");

    // Si el valor de vigenciadias es una fecha (contiene "-"), calculamos la diferencia en días.
    if (strpos($vigenciaInput, "-") !== false) {
      try {
        $fechaVigencia = new DateTime($vigenciaInput);
        // Usamos $fechahora para la fecha de cotización, o la fecha actual si no se definió
        $fechaCotizacion = !empty($fechahora)
          ? new DateTime($fechahora)
          : new DateTime();
        $intervalo = $fechaCotizacion->diff($fechaVigencia);
        $vigenciadias = $intervalo->days;
      } catch (Exception $e) {
        error_log("Error al convertir la fecha de vigencia: " . $e->getMessage());
        $vigenciadias = 0;
      }
    } else {
      // Si ya es un número (por ejemplo, enviado desde JavaScript), se usa directamente
      $vigenciadias = intval($vigenciaInput);
    }

    // Si $fechahora está vacío, asignamos la fecha y hora actual
    if (empty($fechahora)) {
      $fechahora = date("Y-m-d H:i:s");
    } elseif (strpos($fechahora, ' ') === false) {
      $fechahora .= " " . date("H:i:s");
    }

    $moneda = Helper::limpiarCadena($dataJSON['moneda'] ?? "");
    $idcliente = $dataJSON['idcliente'] ?? 0;
    $productos = $dataJSON['productos'] ?? [];

    if (empty($productos)) {
      echo json_encode(["status" => "error", "message" => "No se enviaron productos."]);
      exit;
    }
    error_log("Datos recibidos: " . print_r($dataJSON, true));

    $cotizacion = new Cotizacion();
    $idCotInsertada = $cotizacion->registerCotizacion([
      "fechahora" => $fechahora,
      "vigenciadias" => $vigenciadias,
      "moneda" => $moneda,
      "idcolaborador" => $idadmin,
      "idcliente" => $idcliente,
      "productos" => $productos
    ]);
    if ($idCotInsertada > 0) {
      echo json_encode([
        "status" => "success",
        "message" => "Venta registrada con exito.",
        "idcotizacion" => $idCotInsertada
      ]);
    } else {
      echo json_encode(["status" => "error", "message" => "No se pudo registrar la venta."]);
    }
    break;

}


?>