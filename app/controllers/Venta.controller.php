<?php
// Venta.controller.php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set("America/Lima");

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/sesion.php';
require_once __DIR__ . '/../helpers/helper.php';
require_once __DIR__ . '/../models/Cotizacion.php';

$venta = new Venta();
$m = new Cotizacion();

switch ($_SERVER['REQUEST_METHOD']) {

  case 'GET':
    $tipo = Helper::limpiarCadena($_GET['type'] ?? '');

    // 1) Monedas
    if ($tipo === 'moneda') {
      echo json_encode($venta->getMonedasVentas());
      exit;
    }

    // 2) Autocompletes (productos o clientes)
    if (isset($_GET['q']) && $_GET['q'] !== '') {
      $termino = $_GET['q'];
      if ($tipo === 'producto') {
        echo json_encode($venta->buscarProducto($termino));
        exit;
      } else {
        echo json_encode($venta->buscarCliente($termino));
      }
      exit;
    }

    // 3) Historial por vehículo (mes / semestral / anual)
    if (
      isset($_GET['action'], $_GET['modo'], $_GET['fecha'], $_GET['idvehiculo'])
      && $_GET['action'] === 'historial'
    ) {
      $modo = in_array($_GET['modo'], ['mes', 'semestral', 'anual'], true) ? $_GET['modo'] : 'mes';
      $fecha = $_GET['fecha'] ?: date('Y-m-d');
      $idvehiculo = intval($_GET['idvehiculo']);
      $estado = isset($_GET['estado']) ? (bool) $_GET['estado'] : true;

      $datos = $venta->listarHistorialPorVehiculo($modo, $fecha, $idvehiculo, $estado);
      echo json_encode([
        'status' => 'success',
        'data' => $datos
      ]);
      exit;
    }

    // 4) Listado por periodo (dia / semana / mes) de todas las ventas
    if (isset($_GET['modo'], $_GET['fecha']) && !isset($_GET['action'])) {
      $modo = in_array($_GET['modo'], ['dia', 'semana', 'mes'], true)
        ? $_GET['modo'] : 'dia';
      $fecha = $_GET['fecha'] ?: date('Y-m-d');

      $ventas = $venta->listarPorPeriodoVentas($modo, $fecha);
      echo json_encode([
        'status' => 'success',
        'data' => $ventas
      ]);
      exit;
    }
    // 4.b) Listado de OT por periodo (dia / semana / mes)
    if (
      isset($_GET['action'], $_GET['modo'], $_GET['fecha'])
      && $_GET['action'] === 'ot_por_periodo'
    ) {
      $modo = in_array($_GET['modo'], ['dia', 'semana', 'mes'], true) ? $_GET['modo'] : 'dia';
      $fecha = $_GET['fecha'] ?: date('Y-m-d');

      $ots = $venta->listarPorPeriodoOT($modo, $fecha);
      echo json_encode([
        'status' => 'success',
        'data' => $ots
      ]);
      exit;
    }

    // 5) Ventas eliminadas
    if (isset($_GET['action']) && $_GET['action'] === 'ventas_eliminadas') {
      $eliminadas = $venta->getVentasEliminadas();
      echo json_encode(['status' => 'success', 'data' => $eliminadas]);
      exit;
    }

    // 6) Justificación de eliminación
    if (
      isset($_GET['action'], $_GET['idventa'])
      && $_GET['action'] === 'justificacion'
    ) {
      $id = intval($_GET['idventa']);
      try {
        $just = $venta->getJustificacion($id);
        if ($just !== null) {
          echo json_encode(['status' => 'success', 'justificacion' => $just]);
        } else {
          echo json_encode(['status' => 'error', 'message' => 'No existe justificación']);
        }
      } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
      }
      exit;
    }

    // 7) Operaciones sobre cotizaciones (getCabecera, getDetalle, getClienteCotizacion)
    if (isset($_GET['action'], $_GET['idcotizacion'])) {
      $task = $_GET['action'];
      $id = intval($_GET['idcotizacion']);
      if ($task === 'getCabecera') {
        $cab = $m->getCabeceraById($id);
        echo json_encode($cab);
      } elseif ($task === 'getDetalle') {
        $det = $m->getDetalleById($id);
        echo json_encode($det);
      } elseif ($task === 'getClienteCotizacion') {
        $cab = $m->getCabeceraById($id);
        echo json_encode([
          'idcliente' => $cab['idcliente'] ?? null,
          'cliente' => $cab['cliente'] ?? null
        ]);
      }
      exit;
    }

    // 8) Propietario de una venta
    if (isset($_GET['action'], $_GET['idventa']) && $_GET['action'] === 'propietario') {
      $idventa = intval($_GET['idventa']);
      try {
        $row = $venta->getPropietarioById($idventa);
        if ($row) {
          echo json_encode(['status' => 'success', 'data' => ['propietario' => $row['propietario']]]);
        } else {
          echo json_encode(['status' => 'error', 'message' => 'Venta no encontrada']);
        }
      } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
      }
      exit;
    }

    // 9) Detalle completo de venta para PDF
    if (isset($_GET['action'], $_GET['idventa']) && $_GET['action'] === 'detalle_completo') {
      $venta->detalleCompleto();
      exit;
    }

    // 10) Si no hay parámetro alguno, listamos todas las ventas
    echo json_encode(['status' => 'success', 'data' => $venta->getAll()]);
    exit;

  case 'POST':

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?? $_POST;
    $action = $data['action'] ?? '';

    // 1) Combinar OT
    if ($action === 'combinar_ot') {
      $ids = is_array($data['ids_ot']) ? $data['ids_ot'] : [];
      $tipo = trim($data['tipocom'] ?? '');
      $serie = trim($data['numserie'] ?? '');
      $com = trim($data['numcom'] ?? '');

      try {
        $newId = $venta->combinarOtYCrearVenta($ids, $tipo, $serie, $com);
        echo json_encode([
          'status' => 'success',
          'idventa' => $newId,
          'numserie' => $serie,
          'numcom' => $com
        ]);
      } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
      }
      exit;
    }

    // 10) Anulación de venta (soft‑delete) con justificación
    if (isset($data['action'], $data['idventa']) && $data['action'] === 'eliminar') {
      $id = intval($data['idventa']);
      $justificacion = trim($data['justificacion'] ?? '');
      $ok = $venta->deleteVenta($id, $justificacion);
      echo json_encode([
        'status' => $ok ? 'success' : 'error',
        'message' => $ok ? 'Venta anulada.' : 'No se pudo anular la Venta.'
      ]);
      exit;
    }

    // 11) Registro de venta (con o sin orden de servicio)
    // 1) Determinar si es orden de trabajo
    $conOrden = !empty($data['servicios']);
    $idvehiculo = (!empty($data['idvehiculo']) ? (int) $data['idvehiculo'] : null);
    $kilometraje = isset($data['kilometraje']) ? floatval($data['kilometraje']) : 0;
    if ($conOrden && ($idvehiculo === null || $kilometraje <= 0)) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Para registrar una Orden de Trabajo con servicios, se requieren vehículo y kilometraje válidos.'
      ]);
      exit;
    }


    // Mapeo a NULL si está vacío
    /* $idpropietario = (isset($data['idpropietario']) && $data['idpropietario'] !== '')
      ? (int) $data['idpropietario']
      : null; */
    $idcliente = (isset($data['idcliente']) && $data['idcliente'] !== '')
      ? (int) $data['idcliente']
      : null;

    // 2) Armar parámetros para el método
    $params = [
      'servicios' => $data['servicios'] ?? [],
      'productos' => $data['productos'] ?? [],
      'conOrden' => $conOrden,
      'idcolaborador' => $_SESSION['login']['idcolaborador'],
      'idpropietario' => $data['idpropietario'] ?? 0,
      'idcliente' => (!empty($data['idcliente']) ? (int) $data['idcliente'] : null),
      'idvehiculo' => $idvehiculo,
      'kilometraje' => $kilometraje,
      'observaciones' => trim($data['observaciones'] ?? ''),
      'ingresogrua' => !empty($data['ingresogrua']) ? 1 : 0,
      'fechaingreso' => $data['fechaingreso'] ?? null,
      'tipocom' => $data['tipocom'] ?? '',
      'fechahora' => $data['fechahora'] ?? null,
      'numserie' => $data['numserie'] ?? '',
      'numcom' => $data['numcom'] ?? '',
      'moneda' => $data['moneda'] ?? '',
    ];

    try {
      $res = $venta->registerVentasConOrden($params);
      echo json_encode([
        'status' => 'success',
        'idventa' => $res['idventa'],
        'idorden' => $res['idorden']
      ]);
    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;


  default:
    // Método no soportado
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}
